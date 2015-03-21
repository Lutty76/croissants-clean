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

class DefaultController extends Controller {

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

	    return $this->redirect($this->generateUrl("listUser"));
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
	return $this->redirect($this->generateUrl("listUser"));
    }

    /**
     * @Route("/choseUser")
     * @Template()
     */
    public function selectUserAction() {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('persoCroissantBundle:user')->findAll();
	$token = uniqid();
	$arrayCoef = array();

	// Verifier si personne ne s'est pas déja proposé
	$historyCroissant = $em->createQuery('
	SELECT h FROM persoCroissantBundle:history h
	WHERE h.dateCroissant >= :date_from')->setParameter('date_from', date("Y-m-d H:i:s", strtotime("-1 weeks")))->getResult();
	if (sizeof($historyCroissant) == 0) {
	    foreach ($user as $one)
		for ($i = 0; $i < $one->getCoefficient(); $i++)
		    array_push($arrayCoef, $one->getId());

	    shuffle($arrayCoef);
	    echo '|' . $token . '|';

	    $rand = rand(0, sizeof($arrayCoef) - 1);
	    $user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findOneById($arrayCoef[$rand]);
	    unset($arrayCoef[$rand]);
	    $arrayCoef = array_values($arrayCoef); //Array_slice will be better 
	    $historyUser = $em->createQuery('
	    SELECT h FROM persoCroissantBundle:history h
	    WHERE h.dateCroissant >= :date_from')->setParameter('date_from', date("Y-m-d H:i:s", strtotime("-3 weeks")))->getResult();


	    while (sizeof($historyUser) != 0) {
		unset($arrayCoef[$rand]);
		$arrayCoef = array_values($arrayCoef); //Array_slice will be better 

		if (sizeof($arrayCoef) > 0) {
		    $rand = rand(0, sizeof($arrayCoef) - 1);
		    $user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findOneById($arrayCoef[$rand]);

		    $historyUser = $em->createQuery('
		    SELECT h FROM persoCroissantBundle:history h
		    WHERE h.dateCroissant >= :date_from AND h.idUser = :idUser')->setParameter('date_from', date("Y-m-d H:i:s", strtotime("-3 weeks")))->setParameter('idUser', $user->getId())->getResult();
		} else {
		    return $this->render('persoCroissantBundle::notFoundUser.html.twig');
		}
	    }
	    $user->setToken($token);
	    $history = new \perso\CroissantBundle\Entity\history();
	    $history->setIdUser($user->getId());
	    $history->setDateCroissant(new DateTime(date("Y-M-d")));
	    $history->setOk(0);
	    $em->persist($history);
	    $em->flush();

	    $message = \Swift_Message::newInstance()
		    ->setSubject('Vous avez été tiré au sort pour les croissants !')
		    ->setFrom('kevin@creativedata.fr')
		    ->setTo("kevin@creativedata.fr") //TODO set good email $user->etEmail();
		    ->setBody($this->renderView('persoCroissantBundle::email.txt.twig', array('user' => $user)))
		    ->addPart($this->renderView('persoCroissantBundle::email.html.twig', array('user' => $user)), "text/html");
	    $this->get('mailer')->send($message);
	} else {
	    return new Response(json_encode("ok"));
	}
	return $this->render('persoCroissantBundle::chose.html.twig', array('chosen' => $user));
    }

    /**
     * @Route("/profil/{id}",name="_profil")
     * @Template()
     */
    public function userProfilAction($id) {

	$user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findOneById($id);
	$history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findOneByIdUser($id);
	return $this->render('persoCroissantBundle::profil.html.twig', array('user' => $user, "history" => $history));
    }

    /**
     * @Route("/offer/{id}",name="_offer")
     * @Template()
     */
    public function userAskAction($id) {

	$em = $this->getDoctrine()->getManager();
	$historyCroissant = $em->createQuery('
	SELECT h FROM persoCroissantBundle:history h
	WHERE h.dateCroissant >= :date_from')->setParameter('date_from', date("Y-m-d H:i:s", strtotime("-1 weeks")))->getResult();
	if (sizeof($historyCroissant) == 0) {

	    $history = new \perso\CroissantBundle\Entity\history();
	    $history->setIdUser($id);
	    $history->setDateCroissant(new DateTime(date("Y-M-d")));
	    $history->setOk(1);
	    $em->persist($history);
	    $em->flush();

	    return $this->render('persoCroissantBundle::offer.html.twig', array("msg"=>"Merci pour les croissants ! "));
	} else {
	    return $this->render('persoCroissantBundle::offer.html.twig', array("msg"=>"Quelqu'un s'est déjà proposé cette semaine, merci quand même !"));
	}
    }

    /**
     * @Route("/userAccept/{token}")
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

	if ($user->getCoefficient() > 1) {
	    $user->setCoefficient($user->getCoefficient() - 1);
	    $em->flush();
	}

	return $this->render('persoCroissantBundle::userAccept.html.twig', array('chosen' => $user));
    }

    /**
     * @Route("/userDecline/{token}")
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
     * @Route("/trapUser/{id}")
     * @Template()
     */
    public function trapUserAction($id) {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('persoCroissantBundle:user')->findOneById($id);
	if ($user->getCoefficient() < 5) {
	    $user->setCoefficient($user->getCoefficient() + 1);
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
     * @Route("/sendEmail")
     * @Template()
     */
    public function sendEmailAction() {
	$em = $this->getDoctrine()->getManager();
	$history = $em->getRepository('persoCroissantBundle:history')->findOneByOk(1);
	$user = $em->getRepository('persoCroissantBundle:user')->findOneById($history->getIdUser());

	$message = \Swift_Message::newInstance()
		->setSubject($user->getName() . ' a été tiré au sort pour les croissants !')
		->setFrom('kevin@creativedata.fr')
		->setTo("kevin@creativedata.fr") //TODO set good email $user->etEmail();
		->setBody($user->getName() . " ramènera les croissants demain !")
		->addPart($user->getName() . " ramènera les croissants demain !");
	$this->get('mailer')->send($message);

	return new Response(json_encode("ok"));
    }

    /**
     * @Route("/admin/truncateHistory")
     * @Template()
     */
    public function truncateHistoryAction() {
	$em = $this->getDoctrine()->getManager();
	$connection = $em->getConnection();
	$platform = $connection->getDatabasePlatform();

	$connection->executeUpdate($platform->getTruncateTableSQL('history', true /* whether to cascade */));
	return new Response(json_encode("ok"));
    }

}
