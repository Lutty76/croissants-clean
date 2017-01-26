<?php

namespace CreativeData\CroissantBundle\UserChose;

use \DateTime;
use \Symfony\Bundle\TwigBundle\TwigEngine;

class UserChose {

    protected $em;
    protected $template;
    protected $mailer;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, TwigEngine $template, \Swift_Mailer $mailer)
    {      
        $this->em = $em;
        $this->template = $template;
        $this->mailer = $mailer;
    }
    function getUser($userList) {
	$token = uniqid();
	$arrayCoef = array();
	
		//On alimente le tableau avec les utilisateurs, on les rentre autant de fois que leur coef
	foreach ($userList as $one)
		for ($i = 0; $i < $one->getCoefficient(); $i++)
		    array_push($arrayCoef, $one->getId());

		// On melange le tableau
	    shuffle($arrayCoef);
	   
		
		// On choisi un indice aléatoire (double aleatoire du coup)
		$rand = rand(0, sizeof($arrayCoef) - 1);
	    $user = $this->em->getRepository('CreativeDataCroissantBundle:User')->findOneById($arrayCoef[$rand]);
		
		// On enleve l'element tirer du tableau
	    unset($arrayCoef[$rand]);
	    $arrayCoef = array_values($arrayCoef); //Array_slice will be better 
		
		// On regarde si l'user a amener les croissant il y a moins de trois semaine
	    $historyUser = $this->em->getRepository('CreativeDataCroissantBundle:History')->findAllFromDateAndUser(date("Y-m-d 00:00:00", strtotime("-3 weeks")),date("Y-m-d 00:00:00", strtotime("+3 weeks")), $user);
	   
		// Tant que oui on choisit un autre user
	    while (sizeof($historyUser) != 0) {
		unset($arrayCoef[$rand]);
		$arrayCoef = array_values($arrayCoef); //Array_slice will be better 
		
		//Tant que le tableau n'est pas vide on continu
		if (sizeof($arrayCoef) > 0) {
		    $rand = rand(0, sizeof($arrayCoef) - 1);
		    $user = $this->em->getRepository('CreativeDataCroissantBundle:User')->findOneById($arrayCoef[$rand]);

		    $historyUser = $this->em->getRepository('CreativeDataCroissantBundle:History')->findAllFromDateAndUser(date("Y-m-d 00:00:00", strtotime("-3 weeks")),date("Y-m-d 00:00:00", strtotime("+3 weeks")), $user);
		} else {
		//Sinon on annule le tirage
		    return array();
		}
	    }
		
		//On crée un token présent dans le mail pour pouvoir accepter ou refusé
	    $user->setToken($token);
	    $history = new \CreativeData\CroissantBundle\Entity\History();
	    $history->setUser($user);
	    $history->setDateCroissant(new DateTime(date("Y-M-d")));
	    $history->setOk(0);
	    $this->em->persist($history);
	    $this->em->flush();
		//On envoie le mail à la personne concerné
	    $message = \Swift_Message::newInstance()
		    ->setSubject('Vous avez été tiré au sort pour les croissants !')
		    ->setFrom('devops@creativedata.fr')
		    ->setTo($user->getEmail()) //TODO set good email $user->etEmail();
		    ->setBody($this->template->render('CreativeDataCroissantBundle:email:email.txt.twig', array('user' => $user)))
		    ->addPart($this->template->render('CreativeDataCroissantBundle:email:email.html.twig', array('user' => $user)), "text/html");
	    $this->mailer->send($message);
	    return $user;
    }

}