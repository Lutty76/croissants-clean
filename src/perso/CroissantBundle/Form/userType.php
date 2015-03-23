<?php

// src/OC/PlatformBundle/Form/AdvertType.php

namespace perso\CroissantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class userType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
	$builder
		->add('username', 'text')
		->add('email', 'text')
		->add('premium', 'checkbox', array('required' => false))
		->add('save', 'submit')

	;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
	$resolver->setDefaults(array(
	    'data_class' => 'perso\CroissantBundle\Entity\user'
	));
    }

    public function getName() {
	return 'perso_croissantbundle_user';
    }

}
