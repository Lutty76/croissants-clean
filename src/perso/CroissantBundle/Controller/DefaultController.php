<?php

namespace perso\CroissantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;
use perso\CroissantBundle\Entity\user;
use perso\CroissantBundle\Entity\history;
use perso\CroissantBundle\Form\userType;
use perso\CroissantBundle\Form\historyType;
use HWI\Bundle\OAuthBundle;

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
	$user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findAll();
	return $this->render('persoCroissantBundle::listUser.html.twig', array('users' => $user));
    }

    /**
     * @Route("/listHistory",name="_historyList")
     * @Template()
     */
    public function listHistoryPublicAction() {
	$history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findAllPublic();
	return $this->render('persoCroissantBundle::listHistory.html.twig', array('historys' => $history));
    }

    /**
     * @Route("/admin/listHistory")
     * @Template()
     */
    public function listHistoryAction() {
	$history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findAll();
	return $this->render('persoCroissantBundle::listHistory.html.twig', array('historys' => $history));
    }
    
    /**
     * @Route("/stats",name="_stats")
     * @Template()
     */
    public function statistiqueAction() {
	$history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findAll();
	return $this->render('persoCroissantBundle::stats.html.twig', array('historys' => $history));
    }
    
    /**
     * @Route("/statsToChose")
     * @Template()
     */
    public function statToChoseAction() {
	$user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findAll();
        $data = array();
        foreach($user as $one)
          $data[$one->getName()]=$one->getCoefficient();
        
	return new Response(json_encode($data));
    }
    /**
     * @Route("/statsChosen")
     * @Template()
     */
    public function statChosenAction() {
	$history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findAllToDate(date("Y-m-d 00:00:00"));
        $data = array();
        foreach($history as $one)
          if (key_exists($one->getIdUser(), $data))
            $data[$one->getIdUser()]= $data[$one->getIdUser()]+1; 
          else
            $data[$one->getIdUser()]=0;
        
	return new Response(json_encode($data));
    }
    /**
     * @Route("/admin/addUser")
     * @Template()
     */
    public function addUserAction(Request $request) {
	// crée une tâche et lui donne quelques données par défaut pour cet exemple
	$user = new \perso\CroissantBundle\Entity\user();
	$form = $this->get("form.factory")->create(new userType(), $user);

	$form->handleRequest($request);

	if ($form->isValid()) {
	    $em = $this->getDoctrine()->getManager();
	    if ($user->getPremium())
		$user->setJoker(3);
	    $em->persist($user);
	    $em->flush();

	    $request->getSession()->getFlashBag()->add('notice', 'User bien enregistrée.');

	    return $this->redirect($this->generateUrl("_userList"));
	}
	return $this->render('persoCroissantBundle::addUser.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/admin/removeUser/{id}")
     * @Template()
     */
    public function removeUserAction($id) {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('persoCroissantBundle:user')->findOneById($id);

	$em->remove($user);
	$em->flush();
	return $this->redirect($this->generateUrl("_userList"));
    }

    /**
     * @Route("/admin/choseUser")
     * @Template()
     */
    public function selectUserAction() {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('persoCroissantBundle:user')->findAll();


	// Verifier si personne ne s'est pas déja proposé
	$historyCroissant = $em->getRepository('persoCroissantBundle:history')->findAllFromDateNotRefused( date("Y-m-d 00:00:00", strtotime("-1 weeks")));
	
	if (sizeof($historyCroissant) == 0) {
	   $user =  $this->container->get('perso_croissant.my_user_choser')->getUser($user);
	   if (sizeof($user)==0)
	   return $this->render('persoCroissantBundle::notFoundUser.html.twig');
	} else {
	    
	    $user = $em->getRepository('persoCroissantBundle:user')->findOneById($historyCroissant[0]->getIdUser());
	    return $this->render('persoCroissantBundle::chose.html.twig', array('chosen' => $user));
	}
	return $this->render('persoCroissantBundle::chose.html.twig', array('chosen' => $user));
    }

    /**
     * @Route("/profil/{id}",name="_profil")
     * @Template()
     */
    public function userProfilAction($id) {

	$user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findOneById($id);
	$history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findByIdUser($id);
	return $this->render('persoCroissantBundle::profil.html.twig', array('user' => $user, "history" => $history));
    }

    /**
     * @Route("/offer",name="_offer")
     * @Template()
     */
    public function userAskAction(Request $request) {
			$history = new \perso\CroissantBundle\Entity\history();
	$form = $this->get("form.factory")->create(new historyType(), $history);

	$form->handleRequest($request);

	if ($form->isValid()) {
	    $em = $this->getDoctrine()->getManager();
	   
		$history->setIdUser($this->getUser()->getId());
		$history->setOk(1);
	    $em->persist($history);
	    $em->flush();

	    $request->getSession()->getFlashBag()->add('notice', 'Demande bien enregistrée.');

	    return $this->render('persoCroissantBundle::thanks.html.twig', array("msg"=>"Merci pour les croissants ! Demande enregistré pour le ","date"=>$history->getDateCroissant()));
	}
	return $this->render('persoCroissantBundle::offer.html.twig', array('form' => $form->createView()));

    }

    /**
     * @Route("/userAccept/{token}",name="_accept")
     * @Template()
     */
    public function userAcceptAction($token) {


	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('persoCroissantBundle:user')->findOneByToken($token);

	$history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findOneBy(
		array(
		    "dateCroissant" => new DateTime(date("Y-M-d")),
		    "idUser" => $user->getId()
		)
	);

	$history->setOk(1);
	$em->flush();

	if ($user->getCoefficient() > 2) {
	    $user->setCoefficient($user->getCoefficient() -2);
	    $em->flush();
	}
		else{
	    $user->setCoefficient(1);
	    $em->flush();
	}

	$message = \Swift_Message::newInstance()
		->setSubject($user->getUsername() . ' a été tiré au sort pour les croissants !')
		->setFrom('kevin@creativedata.fr')
		->setTo("all-seineinno@creativedata.fr") //TODO set good email $user->etEmail();
		->setBody($user->getUsername() . " ramènera les croissants demain !")
		->addPart($user->getUsername() . " ramènera les croissants demain !");
	$this->get('mailer')->send($message);
	return $this->render('persoCroissantBundle::userAccept.html.twig', array('chosen' => $user));
    }

    /**
     * @Route("/userDecline/{token}",name="_decline")
     * @Template()
     */
    public function userDeclineAction($token) {


	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('persoCroissantBundle:user')->findOneByToken($token);

	$history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findOneBy(
		array(
		    "dateCroissant" => new DateTime(date("Y-M-d")),
		    "idUser" => $user->getId()
		)
	);

	if ($user->getJoker() > 0) {
	    $user->setJoker($user->getJoker() - 1);
	    $history->setOk(2);
	    $em->flush();
	    return $this->render('persoCroissantBundle::userDecline.html.twig', array('chosen' => $user));
	} else {
	    $history->setOk(1);
	    $em->flush();
	    return $this->render('persoCroissantBundle::userAccept.html.twig', array('chosen' => $user));
	}
    }

    /**
     * @Route("/trap")
     * @Template()
     */
    public function trapUserAction() {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('persoCroissantBundle:user')->findOneById($this->getUser()->getId());
		
	if ($user->getCoefficient() < 5 && ($user->getLastTrap()<new DateTime(date("Y-m-d H:i:s",strtotime("-1 hour"))))) {
	    $user->setCoefficient($user->getCoefficient() + 1);
	    $user->setlastTrap(new DateTime(date("Y-m-d H:i:s")));
	    $em->flush();
	}
	return $this->render('persoCroissantBundle::trapUser.html.twig', array('user' => $user, 'dateFlag' => new DateTime(), "ipUser" => $_SERVER['REMOTE_ADDR']));
    }

    /**
     * @Route("/forceAccept")
     * @Template()
     */
    public function forceAcceptAction() {
	$em = $this->getDoctrine()->getManager();
	$history = $em->getRepository('persoCroissantBundle:history')->findOneByOk(0);

	$history->setOk(1);
	$em->flush();

	return new Response(json_encode("ok"));
    }

    /**
     * @Route("/admin/resetJoker")
     * @Template()
     */
    public function resetJokerAction() {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('persoCroissantBundle:user')->findAll();
	   
	foreach($user as $one)
	{
	    if($one->getPremium() == 1)
		$one->setJoker(3);
	    else
		$one->setJoker(1);
		
	}
	$em->flush();

	return new Response(json_encode("ok"));
    }

    /**
     * @Route("/admin/sendEmail")
     * @Template()
     */
    public function sendEmailAction() {
	$em = $this->getDoctrine()->getManager();
	$history = $em->getRepository('persoCroissantBundle:history')->findOneByOk(1);
	$user = $em->getRepository('persoCroissantBundle:user')->findOneById($history->getIdUser());

	$message = \Swift_Message::newInstance()
		->setSubject($user->getUsername() . ' a été tiré au sort pour les croissants !')
		->setFrom('kevin@creativedata.fr')
		->setTo("all-seineinno@creativedata.fr") //TODO set good email $user->etEmail();
		->setBody($user->getUsername() . " ramènera les croissants demain !")
		->addPart($user->getUsername() . " ramènera les croissants demain !");
	$this->get('mailer')->send($message);

	return new Response(json_encode("ok"));
    }

    /**
     * @Route("/admin/truncateHistory")
     * @Template()
     */
    public function truncateHistoryAction() {
	$em = $this->getDoctrine()->getManager();
/*	$connection = $em->getConnection();
	$platform = $connection->getDatabasePlatform();

	$connection->executeUpdate($platform->getTruncateTableSQL('history', true /* whether to cascade ));*/
	$historyCroissant = $em->getRepository('persoCroissantBundle:history')->deleteAll();
	return new Response(json_encode("ok"));
    }
	
    /**
     * @Route("/admin/delHistory")
     * @Template()
     */
    public function delHistoryAction() {
	$em = $this->getDoctrine()->getManager();
/*	$connection = $em->getConnection();
	$platform = $connection->getDatabasePlatform();

	$connection->executeUpdate($platform->getTruncateTableSQL('history', true /* whether to cascade ));*/
	$historyCroissant = $em->getRepository('persoCroissantBundle:history')->deleteAll();
	return new Response(json_encode("ok"));
    }

}
