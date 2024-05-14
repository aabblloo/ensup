<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\User;
use AppBundle\Utils\EtudiantPeriode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('user', EntityType::class, [
                'label' => 'Utilisateur',
                'class' => User::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'placeholder' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control chosen-select',
                    'data-placeholder' => 'Etudiant',
                ],
            ])
            ->add('debut', DateType::class, [
                'label' => 'Date début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('fin', DateType::class, [
                'label' => 'Date fin',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LogSearch::class,
            'attr' => [
                // 'class' => 'form-inline',
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
