<?php

namespace CreativeData\CroissantBundle\Auth;
 
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use CreativeData\CroissantBundle\Entity\User;
 
class OAuthProvider extends OAuthUserProvider
{
    protected $session, $doctrine, $admins;
 
    public function __construct($session, $doctrine, $service_container)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
    }
 
    public function loadUserByUsername($username)
    {
        $result = $this->doctrine->getRepository('CreativeDataCroissantBundle:User')->findOneByGoogleId($username);
       
        if ($result !== null) {   
            return $result;
        } else {
            return new User();
        }
    }
 
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        //Data from Google response
        $google_id = $response->getUsername(); /* An ID like: 112259658235204980084 */
        
        $email = $response->getEmail();
        $name = $response->getNickname();
 
        //Check if this Google user already exists in our app DB
        $result = $this->doctrine->getRepository('CreativeDataCroissantBundle:User')->findOneByEmail($email);
 
        //add to database if doesn't exists
        if ($result==null) {
            $user = new User();
            $user->setGoogleId($google_id);
            $user->setEmail($email);
            $user->setUsername($name);
            //$user->setRoles('ROLE_USER');
 
            //Set some wild random pass since its irrelevant, this is Google login
            $factory = $this->container->get('security.encoder_factory');
     
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();
        } else {
            $em = $this->doctrine->getManager();
            $user = $result; /* return User */
	    $user->setGoogleId($google_id);
            $em->flush();
        }
 
        return $this->loadUserByUsername($response->getUsername());
    }
 
    public function supportsClass($class)
    {
        return $class === 'CreativeData\\CroissantBundle\\Entity\\User';
    }
}