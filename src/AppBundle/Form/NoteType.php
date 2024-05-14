<?php

namespace AppBundle\Form;

use AppBundle\Entity\Etudiant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('noteClasse', NumberType::class, ['required' => false])
            ->add('noteCompo', NumberType::class, ['required' => false])
            // ->add('coeff', NumberType::class, [
            //     'required' => false,
            //     'attr' => ['disabled' => 'disabled']
            // ])
            // ->add('moyenne', NumberType::class, [
            //     'required' => false,
            //     'attr' => ['disabled' => 'disabled']
            // ])
            //->add('evalution')
            // ->add('etudiant', EntityType::class, [
            //     'class' => Etudiant::class,
            //     'choice_value' => 'id',
            //     'choice_label' => 'prenomNom',
            //     //'attr' => ['disabled' => 'disabled']
            // ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Note'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_note';
    }

}
