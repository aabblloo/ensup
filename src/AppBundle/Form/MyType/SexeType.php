<?php

namespace AppBundle\Form\MyType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SexeType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'choices' => [
                'Homme' => 'H',
                'Femme' => 'F',
            ],
            'placeholder' => '',
            'attr' => ['class' => 'chosen-select']
        ]);
    }

    public function getParent() {
        return ChoiceType::class;
    }

}
