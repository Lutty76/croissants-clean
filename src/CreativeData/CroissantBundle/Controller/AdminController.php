<?php
namespace CreativeData\CroissantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CreativeData\CroissantBundle\Form\UserType;

/**
 * @Route("/admin")
 */

class AdminController extends Controller {

    
    /**
     * @Route("/listHistory")
     * @Template()
     */
    public function listHistoryAction() {
	$history = $this->getDoctrine()->getRepository('CreativeDataCroissantBundle:History')->findAll();
	return $this->render('CreativeDataCroissantBundle::listHistory.html.twig', array('historys' => $history));
    }
    
    
    /**
     * @Route("/addUser")
     * @Template()
     */
    public function addUserAction(Request $request) {
	// crée une tâche et lui donne quelques données par défaut pour cet exemple
	$user = new \CreativeData\CroissantBundle\Entity\User();
	$form = $this->get("form.factory")->create(new UserType(), $user);

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
	return $this->render('CreativeDataCroissantBundle:admin:addUser.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/removeUser/{id}")
     * @Template()
     */
    public function removeUserAction($id) {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('CreativeDataCroissantBundle:User')->findOneById($id);

	$em->remove($user);
	$em->flush();
	return $this->redirect($this->generateUrl("_userList"));
    }

    /**
     * @Route("/choseUser")
     * @Template()
     */
    public function selectUserAction() {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('CreativeDataCroissantBundle:User')->findAll();


	// Verifier si personne ne s'est pas déja proposé
	$historyCroissant = $em->getRepository('CreativeDataCroissantBundle:History')->findAllFromDateNotRefused( date("Y-m-d 00:00:00", strtotime("-5 days")), date("Y-m-d 00:00:00", strtotime("+2 days")));
	
	if (sizeof($historyCroissant) == 0) {
	   $user =  $this->container->get('CreativeData_croissant.my_user_choser')->getUser($user);
	   if (sizeof($user)==0)
	   return $this->render('CreativeDataCroissantBundle:admin:notFoundUser.html.twig');
	} else {
	    
	    return $this->render('CreativeDataCroissantBundle:admin:chose.html.twig', array('chosen' => $historyCroissant[0]->getUser()));
	}
	return $this->render('CreativeDataCroissantBundle:admin:chose.html.twig', array('chosen' => $user));
    }

    /**
     * @Route("/forceAccept")
     * @Template()
     */
    public function forceAcceptAction() {
	$em = $this->getDoctrine()->getManager();
	$history = $em->getRepository('CreativeDataCroissantBundle:History')->findOneByOk(0);

	$history->setOk(1);
	$em->flush();

	return new Response(json_encode("ok"));
    }

    /**
     * @Route("/resetJoker")
     * @Template()
     */
    public function resetJokerAction() {
	$em = $this->getDoctrine()->getManager();
	$users = $em->getRepository('CreativeDataCroissantBundle:user')->findAll();
	   
	foreach($users as $user)
	{
	    if($user->getPremium() == 1)
		$user->setJoker(3);
	    else
		$user->setJoker(1);
		
	}
	$em->flush();

	return new Response(json_encode("ok"));
    }

    /**
     * @Route("/sendEmail")
     * @Template()
     */
    public function sendEmailAction() {
	$em = $this->getDoctrine()->getManager();
	$history = $em->getRepository('CreativeDataCroissantBundle:History')->findTomorrow();
        if (count($history)==0)
        {
            return new Response("nobody");
        }
	$user = $history[0]->getUser();

	$message = \Swift_Message::newInstance()
		->setSubject($user->getUsername() . ' a été tiré au sort pour les croissants !')
		->setFrom('kevin@creativedata.fr')
		->setTo("all-seineinno@creativedata.fr") //TODO set good email $user->etEmail();
		->setBody($user->getUsername() . " ramènera les croissants demain !")
		->addPart($user->getUsername() . " ramènera les croissants demain !");
	$this->get('mailer')->send($message);

	return new Response(json_encode($user->getUsername()));
    }

    /**
     * @Route("/truncateHistory")
     * @Template()
     */
    public function truncateHistoryAction() {
	$em = $this->getDoctrine()->getManager();
	$em->getRepository('CreativeDataCroissantBundle:History')->deleteAll();
	return new Response(json_encode("ok"));
    }
    /**
     * @Route("/upAll")
     * @Template()
     */
    public function upAllAction() {
	$em = $this->getDoctrine()->getManager();
	$users = $em->getRepository('CreativeDataCroissantBundle:User')->findAll();
        
        foreach($users as $user){
            $user->setCoef($user->getCoef()+1);
        }
        $em->flush();
        
	return new Response(json_encode("ok"));
    }
}