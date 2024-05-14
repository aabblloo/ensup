<?php

namespace AppBundle\Form;

use AppBundle\Entity\Dossier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DossierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', ChoiceType::class, [
            'choices' => Dossier::getFileTypes(),
            'choice_label' => function ($choice) {
                return $choice;
            },
            'placeholder' => '',
            'attr' => ['class' => 'chosen-select']
        ])
        ->add('file', FileType::class, ['label' => 'Document'])
            // ->add('lien')
            // ->add('etudiant')
            // ->add('prof')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dossier::class,
        ]);
    }
}
