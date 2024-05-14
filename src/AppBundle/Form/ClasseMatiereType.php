<?php

namespace AppBundle\Form;

use AppBundle\Entity\Matiere;
use AppBundle\Entity\MyConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseMatiereType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coeff', NumberType::class, ['label' => 'Coefficient'])
            ->add('matiere', EntityType::class, [
                'label' => 'Module',
                'class' => Matiere::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.code', 'ASC');
                },
                'choice_value' => 'id',
                'choice_label' => 'code',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT],
            ])
            // ->add('file', null, ['label' => 'Document'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ClasseMatiere',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_classematiere';
    }

}
