<?php

namespace AppBundle\Form\MyType;

use App\Entity\Centre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CentreType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'class' => Centre::class,
            'choice_value' => 'id',
            'choice_label' => 'nom',
            'placeholder' => '',
            'attr' => ['class' => 'chosen-select']
        ]);
    }

    public function getParent() {
        return EntityType::class;
    }

}
