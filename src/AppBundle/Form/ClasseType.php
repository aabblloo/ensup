<?php

namespace AppBundle\Form;

use AppBundle\Entity\Classe;
use AppBundle\Entity\Cycle;
use AppBundle\Entity\MyConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('nom', TextareaType::class)
            ->add('cycle', EntityType::class, [
                'class' => Cycle::class,
                'label' => "Niveau d'étude",
                'choice_value' => 'id',
                'choice_label' => 'code',
                'placeholder' => '',
                'attr' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => MyConfig::CHOSEN_TEXT
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Classe::class,
            ]
        );
    }
}
