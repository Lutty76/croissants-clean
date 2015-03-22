<?php

namespace perso\CroissantBundle\UserChose;

use \DateTime;

class UserChose {

    protected $em;
    protected $template;
    protected $mailer;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine $template, \Swift_Mailer $mailer)
    {      
        $this->em = $em;
        $this->template = $template;
        $this->mailer = $mailer;
    }
    function getUser($userList) {
	$token = uniqid();
	$arrayCoef = array();
	
	foreach ($userList as $one)
		for ($i = 0; $i < $one->getCoefficient(); $i++)
		    array_push($arrayCoef, $one->getId());

	    shuffle($arrayCoef);
	    echo '|' . $token . '|';

	    $rand = rand(0, sizeof($arrayCoef) - 1);
	    $user = $this->em->getRepository('persoCroissantBundle:user')->findOneById($arrayCoef[$rand]);
	    unset($arrayCoef[$rand]);
	    $arrayCoef = array_values($arrayCoef); //Array_slice will be better 
	    $historyUser = $this->em->getRepository('persoCroissantBundle:history')->findAllFromDateAndIdUser(date("Y-m-d 00:00:00", strtotime("-3 weeks")), $user->getId());
	   echo sizeof($historyUser);

	    while (sizeof($historyUser) != 0) {
		unset($arrayCoef[$rand]);
		$arrayCoef = array_values($arrayCoef); //Array_slice will be better 

		if (sizeof($arrayCoef) > 0) {
		    $rand = rand(0, sizeof($arrayCoef) - 1);
		    $user = $this->em->getRepository('persoCroissantBundle:user')->findOneById($arrayCoef[$rand]);

		    $historyUser = $this->em->getRepository('persoCroissantBundle:history')->findAllFromDateAndIdUser(date("Y-m-d 00:00:00", strtotime("-3 weeks")), $user->getId());
		} else {
		    return array();
		}
	    }
	    $user->setToken($token);
	    $history = new \perso\CroissantBundle\Entity\history();
	    $history->setIdUser($user->getId());
	    $history->setDateCroissant(new DateTime(date("Y-M-d")));
	    $history->setOk(0);
	    $this->em->persist($history);
	    $this->em->flush();

	    $message = \Swift_Message::newInstance()
		    ->setSubject('Vous avez été tiré au sort pour les croissants !')
		    ->setFrom('kevin@creativedata.fr')
		    ->setTo("kevin@creativedata.fr") //TODO set good email $user->etEmail();
		    ->setBody($this->template->render('persoCroissantBundle::email.txt.twig', array('user' => $user)))
		    ->addPart($this->template->render('persoCroissantBundle::email.html.twig', array('user' => $user)), "text/html");
	    $this->mailer->send($message);
	    return $user;
    }

}
