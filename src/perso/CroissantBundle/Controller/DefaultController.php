<?php

namespace perso\CroissantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use perso\CroissantBundle\Entity\user;
use perso\CroissantBundle\Form\userType;


class DefaultController extends Controller
{
    /**
     * @Route("/listUser")
     * @Template()
     */
    public function indexAction()
    {

          $user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findAll();
        return $this->render('persoCroissantBundle::listUser.html.twig',array('users'=> $user ));
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
       $arrayCoef = array();
        foreach($user as $one)
          for ($i=0;$i<$one->getCoefficient();$i++)
              array_push($arrayCoef,$one->getId());
            shuffle ($arrayCoef);
print_r($arrayCoef);
         $user = $this->getDoctrine()->getRepository('persoCroissantBundle:user')->findOneById($arrayCoef[rand(0,sizeof($arrayCoef)-1)] );
                 if ($user->getCoefficient()>1){
         $user->setCoefficient($user->getCoefficient()-1);
         $em->flush();}
        return $this->render('persoCroissantBundle::chose.html.twig',array('chosen'=> $user ));

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
        return $this->render('persoCroissantBundle::trapUser.html.twig',array('user'=>$user));
    }


}
