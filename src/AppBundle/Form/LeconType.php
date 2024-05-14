<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeconType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('debut', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('fin', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('description')
            ->add('docFile', null, array('label' => 'Document', 'required' => false))
            ->add('videoFile', null, array('label' => 'Vidéo', 'required' => false))
            // ->add('classeMatiere')
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Lecon'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_lecon';
    }
}
