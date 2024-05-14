<?php

namespace AppBundle\Form;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Banque;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Paiement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('montant', NumberType::class)
            ->add('anScolaire', EntityType::class, [
                'label' => 'Année scolaire',
                'class' => AnScolaire::class,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT],
            ])
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_value' => 'id',
                'choice_label' => 'mlePrenomNom',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
            'attr' => [
                'autocomplete' => 'off',
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
