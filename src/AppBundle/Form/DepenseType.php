<?php

namespace AppBundle\Form;

use AppBundle\Entity\Depense;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\AnScolaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('ref')
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('montant')
            ->add('anScolaire', EntityType::class, [
                'label' => 'Année scolaire',
                'class' => AnScolaire::class,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => Depense::getTypesDepense(),
                'choice_label' => function ($choice) {
                    return $choice;
                },
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Depense::class,
        ]);
    }
}
