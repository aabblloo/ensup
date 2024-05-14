<?php

namespace AppBundle\Form\MyType;

use AppBundle\Entity\EtudiantClasse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LettreType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'choices' => EtudiantClasse::getLettres(),
            'choice_label' => function ($choice) {
                return $choice;
            },
            'placeholder' => '',
            'required' => false,
            'attr' => ['class' => 'chosen-select'],
        ]);
    }

    public function getParent() {
        return ChoiceType::class;
    }

}
