<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Etudiant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantPeriodeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('get')
            ->add('etudiant', EntityType::class, [
                'label' => 'Etudiant',
                'class' => Etudiant::class,
                'choice_value' => 'id',
                'choice_label' => 'prenomNomMle',
                'placeholder' => '',
                'required' => false,
                'attr' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Etudiant',
                ],
            ])
            ->add('debut', DateType::class, [
                'label' => 'Date début',
                'widget' => 'single_text'
            ])
            ->add('fin', DateType::class, [
                'label' => 'Date fin',
                'widget' => 'single_text'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EtudiantPeriode::class,
            // 'attr' => ['novalidate' => 'novalidate']
        ]);
    }

}
