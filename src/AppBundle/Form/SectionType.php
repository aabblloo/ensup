<?php

namespace AppBundle\Form;

use AppBundle\Entity\Departement;
use AppBundle\Entity\MyConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('nom', TextareaType::class)
            ->add('departement', EntityType::class, [
                'label' => 'Département',
                'class' => Departement::class,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select',
                    'data-placeholder' => MyConfig::CHOSEN_TEXT],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Section'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_section';
    }


}
