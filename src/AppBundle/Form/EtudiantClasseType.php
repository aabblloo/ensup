<?php

namespace AppBundle\Form;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\MyConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use AppBundle\Form\MyType\SpecialiteType;

class EtudiantClasseType extends AbstractType
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montant', NumberType::class, ['label' => 'Montant dû'])
            ->add('montantPaye', NumberType::class, ['label' => 'Montant payé'])
            ->add('date', DateType::class, [
                'label' => "Date d'inscription",
                'widget' => 'single_text'
            ])
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_value' => 'id',
                'choice_label' => 'prenomNomMle',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select'],
            ])
            ->add('anScolaire', EntityType::class, [
                'label' => 'Année scolaire',
                'class' => AnScolaire::class,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT],
            ])
            ->add('specialite', SpecialiteType::class)
            ->add('classe', \AppBundle\Form\MyType\ClasseType::class)
            ->add('lettre', ChoiceType::class, [
                'choices' => EtudiantClasse::getLettres(),
                'choice_label' => function ($choice) {
                    return $choice;
                },
                'placeholder' => '',
                'required' => false,
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EtudiantClasse::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
