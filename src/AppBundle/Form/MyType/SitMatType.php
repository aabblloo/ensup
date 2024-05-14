<?php

namespace AppBundle\Form\MyType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SitMatType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getSitMats(),
            'placeholder' => '',
            'attr' => ['class' => 'chosen-select']
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getSitMats()
    {
        // $sitMats = [];
        $liste = ['Célibataire', 'Marié(e)', 'Divorcé(e)', 'Veuf(ve)'];
        $sitMats = array_combine($liste, $liste);
        // foreach ($liste as $value) {
        //     $sitMats[$value] = $value;
        // }
        return $sitMats;
    }
}
