<?php

namespace AppBundle\Form\MyType;

use AppBundle\Entity\Classe;
use AppBundle\Entity\Matiere;
use AppBundle\Entity\MyConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => "Matière",
            'class' => Matiere::class,
            'choice_value' => 'id',
            'choice_label' => 'nom',
            'placeholder' => '',
            'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
