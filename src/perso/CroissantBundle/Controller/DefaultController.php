<?php

namespace perso\CroissantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use perso\CroissantBundle\Entity\user;
use perso\CroissantBundle\Entity\history;
use perso\CroissantBundle\Form\userType;

class DefaultController extends Controller
{
    /**
     * @Route("/listUser")
     * @Template()
     */
    public function listUserAction()
    {

          $user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findAll();
        return $this->render('persoCroissantBundle::listUser.html.twig',array('users'=> $user ));
    }
    /**
     * @Route("/listHistory")
     * @Template()
     */
    public function listHistoryAction()
    {

          $history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findAll();
        return $this->render('persoCroissantBundle::listHistory.html.twig',array('historys'=> $history ));
    }
    /**
     * @Route("/addUser")
     * @Template()
     */
    
    public function addUserAction(Request $request)
    {
        // crée une tâche et lui donne quelques données par défaut pour cet exemple
        $user = new \perso\CroissantBundle\Entity\user();
        $form = $this->get("form.factory")->create(new userType(),$user);
   
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
        return $this->render('persoCroissantBundle::addUser.html.twig', array(
            'form' => $form->createView(),
        ));
    
    } 
    
    /**
     * @Route("/removeUser/{id}")
     * @Template()
     */
    
    public function removeUserAction($id)
    {
    
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
    
    public function selectUserAction()
    { 
       $em = $this->getDoctrine()->getManager();
         $user = $em->getRepository('persoCroissantBundle:user')->findAll(); 
      $token =  uniqid();
       $arrayCoef = array();
        foreach($user as $one)
          for ($i=0;$i<$one->getCoefficient();$i++)
              array_push($arrayCoef,$one->getId());
            shuffle ($arrayCoef);
         print_r($arrayCoef);
         echo '|'.$token.'|';
         $user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findOneById($arrayCoef[rand(0,sizeof($arrayCoef)-1)] );
                
        $user->setToken($token);
        $history = new \perso\CroissantBundle\Entity\history();
        $history->setIdUser($user->getId());
        $history->setDateCroissant(new DateTime(date("Y-M-d")));
        $history->setOk(0);
        $em->persist($history);
        $em->flush();
        
        return $this->render('persoCroissantBundle::chose.html.twig',array('chosen'=> $user ));

    } 
           /**
     * @Route("/userAccept/{token}")
     * @Template()
     */
     public function userAcceptAction($token){   
           
           
       $em = $this->getDoctrine()->getManager();
         $user = $em->getRepository('persoCroissantBundle:user')->findOneByToken($token);
           
         $history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findOneBy(
               array(
                   "dateCroissant" => new DateTime(date("Y-M-d")),
                   "idUser"=> $user->getId()
               ) 
           );
       
        $history->setOk(1);
         $em->flush(); 
       
         if ($user->getCoefficient()>1){
         $user->setCoefficient($user->getCoefficient()-1);
         $em->flush();}
             return $this->render('persoCroissantBundle::userAccept.html.twig',array('chosen' => $user));
           
     }
      /**
     * @Route("/userDecline/{token}")
     * @Template()
     */
     public function userDeclineAction($token){   
           
           
       $em = $this->getDoctrine()->getManager();
         $user = $em->getRepository('persoCroissantBundle:user')->findOneByToken($token);
           
         $history = $this->getDoctrine()->getRepository('persoCroissantBundle:history')->findOneBy(
               array(
                   "dateCroissant" => new DateTime(date("Y-M-d")),
                   "idUser"=> $user->getId()
               ) 
           );
       
         if ($user->getJoker()>0){
       $user->setJoker($user->getJoker()-1);
        $history->setOk(2);
         $em->flush(); 
             return $this->render('persoCroissantBundle::userDecline.html.twig',array('chosen' => $user));
         }else{    
         $history->setOk(1);
         $em->flush(); 
             return $this->render('persoCroissantBundle::userAccept.html.twig',array('chosen' => $user));
         }
     }
           /**
     * @Route("/trapUser/{id}")
     * @Template()
     */
    
    public function trapUserAction($id)
    { 
       $em = $this->getDoctrine()->getManager();
         $user = $em->getRepository('persoCroissantBundle:user')->findOneById($id);
         if ($user->getCoefficient()<5)
         {$user->setCoefficient($user->getCoefficient()+1);
         $em->flush();}
        return $this->render('persoCroissantBundle::trapUser.html.twig',array('user'=>$user,'dateFlag'=> new DateTime(),"ipUser"=> $_SERVER['REMOTE_ADDR']));
    }


}
