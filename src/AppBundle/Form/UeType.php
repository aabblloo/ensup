<?php

namespace AppBundle\Form;

use AppBundle\Form\MyType\SemestreType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('code')
            // ->add('nom', TextareaType::class)
            ->add('credit')
            // ->add('classe', EntityType::class, [
            //     'class' => Classe::class,
            //     'choice_value' => 'id',
            //     'choice_label' => 'nom'
            // ])
            ->add('semestre', SemestreType::class, [
                'required' => true
            ])
            ->add('listeUe', \AppBundle\Form\MyType\ListeUeType::class, [
                'label' => 'UE',
                'required' => true
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ue'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ue';
    }
}
