<?php

namespace AppBundle\Form;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;

class EtudiantClasse2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('anScolaire', EntityType::class, [
                'label' => 'Année scolaire',
                'class' => AnScolaire::class,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select']
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select']
            ])
            ->add('montant');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EtudiantClasse::class,
        ]);
    }
}
