<?php

namespace AppBundle\Form\MyType;

use AppBundle\Entity\ListeUe;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Semestre;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListeUeType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => ListeUe::class,

            'query_builder' => function (EntityRepository $er) {
                $query = $er->createQueryBuilder('listeUe');
                $query->orderBy('listeUe.nom', 'ASC');
                return $query;
            },

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
