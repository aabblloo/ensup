<?php

namespace AppBundle\Form;

use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Parents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParentsEtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('etudiants', EntityType::class,[
                'class'=>EtudiantClasse::class,
                'choice_label'=>'prenomNom'
            ])
            ->add('nom')
            ->add('telephone')
            ->add('email');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Parents::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
