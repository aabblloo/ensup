<?php

namespace AppBundle\Form;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;
use AppBundle\Entity\Evaluation;
use AppBundle\Entity\Matiere;
use AppBundle\Entity\MyConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteCreateType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('anScolaire', EntityType::class, [
                    'label' => 'Année scolaire',
                    'class' => AnScolaire::class,
                    'choice_value' => 'id',
                    'choice_label' => 'nom',
                    'placeholder' => '',
                    'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
                ])
                ->add('evaluation', EntityType::class, [
                    'label' => 'Evaluation',
                    'class' => Evaluation::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                                ->orderBy('e.id', 'ASC');
                    },
                    'choice_value' => 'id',
                    'choice_label' => 'nom',
                    'placeholder' => '',
                    'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
                ])
                ->add('classe', EntityType::class, [
                    'label' => 'Classe',
                    'class' => Classe::class,
                    'choice_value' => 'id',
                    'choice_label' => 'code',
                    'placeholder' => '',
                    'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
                ])
                ->add('matiere', EntityType::class, [
                    'label' => 'Module',
                    'class' => Matiere::class,
                    'choice_value' => 'id',
                    'choice_label' => 'code',
                    'placeholder' => '',
                    'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
                ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Note'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_note';
    }

}
