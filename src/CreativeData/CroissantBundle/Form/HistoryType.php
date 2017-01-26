<?php

// src/OC/PlatformBundle/Form/AdvertType.php

namespace CreativeData\CroissantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HistoryType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
	$builder
		->add('dateCroissant', 'date', array('years' => range(date('Y') , date('Y') +1), 'format' => 'dd MM yyyy'))
		->add('save', 'submit')

	;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
	$resolver->setDefaults(array(
	    'data_class' => 'CreativeData\CroissantBundle\Entity\History'
	));
    }

    public function getName() {
	return 'creativedata_croissantbundle_history';
    }

}
