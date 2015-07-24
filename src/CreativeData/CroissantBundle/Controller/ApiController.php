<?php
namespace CreativeData\CroissantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;


/**
 * @Route("/api")
 */

class AdminController extends Controller {

    
    /**
     * @Route("/api/upCoef/{email}")
     * @Template()
     */
    public function upCoefAction($email) {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('CreativeDataCroissantBundle:User')->findOneByEmail($email);
        if ($user->getCoefficient() < 20 && ($user->getLastUp()<new DateTime(date("Y-m-d H:i:s",strtotime("-1 hour"))))) {
	    $user->setCoefficient($user->getCoefficient() + 1);
	    $user->setlastUp(new DateTime(date("Y-m-d H:i:s")));
	    $em->flush();
	
            return new Response(json_encode("ok"));
        }else{
            return new Response(json_encode("ko"));
        }
    }
    /**
     * @Route("/api/downCoef/{email}")
     * @Template()
     */
    public function downCoefAction($email) {
	$em = $this->getDoctrine()->getManager();
	$user = $em->getRepository('CreativeDataCroissantBundle:User')->findOneByEmail($email);
        
        if ($user->getCoefficient() > 0 ){
            $user->setCoefficient($user->getCoefficient() -1 );
            $user->setlastUp(new DateTime(date("Y-m-d H:i:s")));
            $em->flush();
            return new Response(json_encode("ok"));
        }
        else{
            return new Response(json_encode("ok"));
             
        }
    }
}