<?php

namespace AppBundle\Utils;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Professeur;
use AppBundle\Utils\ProfAnneePeriode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfAnneePeriodeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('prof', EntityType::class, [
                    'label'        => 'Professeur',
                    'class'        => Professeur::class,
                    'choice_value' => 'id',
                    'choice_label' => 'prenomNom',
                    'placeholder'  => '',
                    'required'     => false,
                    'attr'         => [
                        'class'            => 'chosen-select',
                        'data-placeholder' => 'Professeur',
                    ],
                ])
                ->add('annee', EntityType::class, [
                    'label'        => 'Année scolaire',
                    'class'        => AnScolaire::class,
                    'choice_value' => 'id',
                    'choice_label' => 'nom',
                    'placeholder'  => '',
                    'attr'         => [
                        'class'            => 'chosen-select',
                        'data-placeholder' => 'Année',
                    ],
                ])
                ->add('debut', DateType::class, [
                    'label'  => 'Date début',
                    'widget' => 'single_text'
                ])
                ->add('fin', DateType::class, [
                    'label'  => 'Date fin',
                    'widget' => 'single_text'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => ProfAnneePeriode::class,
//            'attr'       => ['novalidate' => 'novalidate']
        ]);
    }

}
