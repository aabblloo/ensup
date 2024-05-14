<?php

namespace AppBundle\Form\MyType;

use App\Entity\Etudiant;
use App\Entity\MyConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtatType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'choices' => Etudiant::getEtats(),
            'choice_label' => function ($choice) {
                return $choice;
            },
            'placeholder' => '',
            'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
        ]);
    }

    public function getParent() {
        return ChoiceType::class;
    }

}
