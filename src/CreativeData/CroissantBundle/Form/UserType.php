<?php

// src/OC/PlatformBundle/Form/AdvertType.php

namespace CreativeData\CroissantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
	$builder
		->add('username', 'text')
		->add('email', 'text')
		->add('save', 'submit')

	;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
	$resolver->setDefaults(array(
	    'data_class' => 'CreativeData\CroissantBundle\Entity\User'
	));
    }

    public function getName() {
	return 'creativedata_croissantbundle_user';
    }

}
