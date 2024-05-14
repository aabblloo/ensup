<?php

namespace AppBundle\Form;

use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Suivi;
use AppBundle\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SuiviType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_value' => 'id',
                'choice_label' => 'mlePrenomNom',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => Suivi::getTypes(),
                'choice_label' => function ($choice) {
                    return $choice;
                },
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
            ])
            ->add('observation');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
        ]);
    }
}
