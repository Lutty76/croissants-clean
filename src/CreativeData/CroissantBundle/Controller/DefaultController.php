<?php
namespace CreativeData\CroissantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;
use CreativeData\CroissantBundle\Form\HistoryType;

class DefaultController extends Controller {

    /**
     * @Route("/")
     * @Template()
     */
    public function homeAction() {
	    return $this->redirect($this->generateUrl("_profil",array("id"=>$this->getUser()->getId())));
    }    
    /**
     * @Route("/listUser",name="_userList")
     * @Template()
     */
    public function listUserAction() {
	$user = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:user')->findAll();
       //print_r($user);
	return $this->render('CreativeDataCroissantBundle::listUser.html.twig', array('users' => $user));
    }

    /**
     * @Route("/listHistory",name="_historyList")
     * @Template()
     */
    public function listHistoryPublicAction() {
	$history = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:History')->findAllPublicFromDate(date("Y-m-d 00:00:00",strtotime("-1 day")));
	return $this->render('CreativeDataCroissantBundle::listHistory.html.twig', array('historys' => $history));
    }

    /**
     * @Route("/stats",name="_stats")
     * @Template()
     */
    public function statistiqueAction() {
	$history = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:History')->findAll();
	return $this->render('CreativeDataCroissantBundle::stats.html.twig', array('historys' => $history));
    }
    
    /**
     * @Route("/statsToChose")
     * @Template()
     */
    public function statToChoseAction() {
	$user = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:User')->findAll();
        $data = array();
        foreach($user as $one)
          array_push($data,array($one->getUsername(),$one->getCoefficient()));
        
	return new Response(json_encode($data));
    }
    /**
     * @Route("/statsChosen")
     * @Template()
     */
    public function statChosenAction() {
	$history = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:History')->findAllToDateAccepted(date("Y-m-d 00:00:00"));
        $data = array(); //ugly
        foreach($history as $one)
          if (key_exists($one->getUser()->getUsername(), $data))
            $data[$one->getUser()->getUsername()]=array($one->getUser()->getUsername(),$data[$one->getUser()->getUsername()][1]+1); 
          else
            $data[$one->getUser()->getUsername()]=array($one->getUser()->getUsername(),1);
        $data = array_values($data);
	return new Response(json_encode($data));
    }
    /**
     * @Route("/profil/{id}",name="_profil")
     * @Template()
     */
    public function userProfilAction($id) {

	$user = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:User')->findOneById($id);
	$history = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:History')->findByIdUser($id);
	return $this->render('CreativeDataCroissantBundle::profil.html.twig', array('user' => $user, "history" => $history));
    }

    /**
     * @Route("/offer",name="_offer")
     * @Template()
     */
    public function userAskAction(Request $request) {
			$history = new \CreativeData\CroissantBundle\Entity\History();
	$form = $this->get("form.factory")->create(new HistoryType(), $history);

	$form->handleRequest($request);

	if ($form->isValid()) {
	    $em = $this->getDoctrine()->getManager();
		$history->setUser($this->getUser());
		$history->setIdUser($this->getUser()->getId());
		$history->setOk(1);
	    $em->persist($history);
	    $em->flush();

	    $request->getSession()->getFlashBag()->add('notice', 'Demande bien enregistrée.');

	    return $this->render('CreativeDataCroissantBundle::thanks.html.twig', array("msg"=>"Merci pour les croissants ! Demande enregistré pour le ","date"=>$history->getDateCroissant()));
	}
	return $this->render('CreativeDataCroissantBundle::offer.html.twig', array('form' => $form->createView()));

    }

    /**
     * @Route("/userAccept/{token}",name="_accept")
     * @Template()
     */
    public function userAcceptAction($token) {


	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('CreativeDataCroissantBundle:User')->findOneByToken($token);

	$history = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:History')->findOneBy(
		array(
		    "dateCroissant" => new DateTime(date("Y-M-d")),
		    "idUser" => $user->getId()
		)
	);

	$history->setOk(1);
	$em->flush();

        $user->setCoefficient(1);
        $em->flush();
	

	$message = \Swift_Message::newInstance()
		->setSubject($user->getUsername() . ' a été tiré au sort pour les croissants !')
		->setFrom('kevin@creativedata.fr')
		->setTo("all-seineinno@creativedata.fr") //TODO set good email $user->etEmail();
		->setBody($user->getUsername() . " ramènera les croissants demain !")
		->addPart($user->getUsername() . " ramènera les croissants demain !");
	$this->get('mailer')->send($message);
	return $this->render('CreativeDataCroissantBundle::userAccept.html.twig', array('chosen' => $user));
    }

    /**
     * @Route("/userDecline/{token}",name="_decline")
     * @Template()
     */
    public function userDeclineAction($token) {


	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('CreativeDataCroissantBundle:User')->findOneByToken($token);

	$history = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:History')->findOneBy(
		array(
		    "dateCroissant" => new DateTime(date("Y-M-d")),
		    "idUser" => $user->getId()
		)
	);

	if ($user->getJoker() > 0) {
	    $user->setJoker($user->getJoker() - 1);
	    $history->setOk(2);
	    $em->flush();
	    return $this->render('CreativeDataCroissantBundle::userDecline.html.twig', array('chosen' => $user));
	} else {
	    $history->setOk(1);
	    $em->flush();
	    return $this->render('CreativeDataCroissantBundle::userAccept.html.twig', array('chosen' => $user));
	}
    }

    /**
     * @Route("/trap",name="_trap")
     * @Template()
     */
    public function trapUserAction() {
	 $form = $this->createFormBuilder()
            ->add('test', 'checkbox', array('data' => true))
            ->add('save', 'submit')
            ->getForm();
	 
    if (isset($_POST['form'])) {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('CreativeDataCroissantBundle:User')->findOneById($this->getUser()->getId());
        if ($user->getCoefficient() < 20 && ($user->getLastTrap()<new DateTime(date("Y-m-d H:i:s",strtotime("-1 hour"))))) {
	    $user->setCoefficient($user->getCoefficient() + 1);
	    $user->setlastTrap(new DateTime(date("Y-m-d H:i:s")));
	    $em->flush();
	}
	return $this->redirect($this->generateUrl("_trapped"));
    }
    return $this->render('CreativeDataCroissantBundle::trapForm.html.twig',array("form"=> $form->createView()));
  
    }
    /**
     * @Route("/trapped",name="_trapped")
     * @Template()
     */
    public function trappedUserAction() {
		
	
	return $this->render('CreativeDataCroissantBundle::trapUser.html.twig', array('user' => $this->getUser(), 'dateFlag' => new DateTime(), "ipUser" => $_SERVER['REMOTE_ADDR']));
    }
}
