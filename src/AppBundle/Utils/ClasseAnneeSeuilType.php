<?php

namespace AppBundle\Utils;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\MyConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseAnneeSeuilType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->setMethod('GET')
                ->add('classe', EntityType::class, [
                    'class' => Classe::class,
                    'choice_value' => 'id',
                    'choice_label' => 'codeNom',
                    'placeholder' => '',
                    'attr' => [
                        'class' => 'chosen-select',
                        'data-placeholder' => 'Classe',
                    ],
                    'required' => false
                ])
                ->add('annee', EntityType::class, [
                    'label' => 'Année scolaire',
                    'class' => AnScolaire::class,
                    'choice_value' => 'id',
                    'choice_label' => 'nom',
                    'placeholder' => '',
                    'attr' => [
                        'class' => 'chosen-select',
                        'data-placeholder' => 'Année',
                    ],
                    'required' => false
                ])
                ->add('lettre', ChoiceType::class, [
                    'choices' => EtudiantClasse::getLettres(),
                    'choice_label' => function ($choice) {
                        return $choice;
                    },
                    'placeholder' => '',
                    'required' => false,
                    'attr' => [
                        'class' => 'chosen-select',
                        'data-placeholder' => MyConfig::CHOSEN_TEXT,
                    ],
                    'required' => false
                ])
                ->add('seuil', ChoiceType::class, [
                    'label' => 'Seuil inférieur à',
                    'choices' => ClasseAnneeSeuil::getSeuils(),
                    'choice_label' => function ($choice) {
                        return $choice;
                    },
                    'placeholder' => '',
                    'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => ClasseAnneeSeuil::class,
                //'attr' => ['novalidate' => 'novalidate']
        ]);
    }

}
