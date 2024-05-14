<?php

namespace AppBundle\Form;

use AppBundle\Entity\Professeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ProfesseurType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add(
                        'prenom', null, ['label' => 'Prénom', 'attr' => ['tabindex' => 1]]
                )
                ->add('nom')
                ->add('specialite', null, ['label' => 'Spécialité', 'required' => false])
                ->add('telephone', null, ['label' => 'Téléphone'])
                ->add('email', null, ['attr' => ['tabindex' => 5]])
                ->add(
                        'dateEntree', DateType::class, [
                    'label' => 'Date entrée',
                    'widget' => 'single_text',
                    'required' => false,
                        ]
                )
                ->add(
                        'dateSortie', DateType::class, [
                    'label' => 'Date sortie',
                    'widget' => 'single_text',
                    'required' => false,
                        ]
                )
                ->add('taux', null, ['label' => 'Taux horaire']);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(
                [
                    'data_class' => Professeur::class,
                ]
        );
    }

}
