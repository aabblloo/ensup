<?php

namespace AppBundle\Form;

use AppBundle\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EtudiantPhotoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('file', FileType::class, [
                    'label' => 'Photo 413 x 531 px',
                    'constraints' => [new NotBlank()],
                    'required' => true
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
                //'attr' => ['novalidate' => 'novalidate']
        ]);
    }

}
