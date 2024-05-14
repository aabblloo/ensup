<?php

namespace AppBundle\Form;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\MyConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matricule')
            ->add('prenom', null, [
                'label' => 'Prénom',
            ])
            ->add('nom')
            ->add('sexe', ChoiceType::class, [
                'choices' => Etudiant::getSexes(),
                'choice_label' => function ($choice) {
                    return $choice;
                },
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
            ])
            ->add('dateNaiss', DateType::class, [
                'label' => 'Date naiss.',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('lieuNaiss')
            ->add('quartier')
            ->add('telephone', TelType::class, ['label' => 'Téléphone', 'required' => false])
            ->add('email')
            // ->add('anneeDef', null, ['label' => 'Année DEF'])
            ->add('anneeBac', null, ['label' => 'Année BAC'])
            ->add('file', FileType::class, ['label' => 'Photo 413 x 531 px', 'required' => false])
            ->add('etat', ChoiceType::class, [
                'choices' => Etudiant::getEtats(),
                'choice_label' => function ($choice) {
                    return $choice;
                },
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
            ])
            ->add('isAccesCours', null, ['label' => 'Accès au cours']);
        // ->add('etudiantClasses', CollectionType::class, [
        //     'entry_type' => EtudiantClasse2Type::class,
        //     'entry_options' => ['label' => false],
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
            //'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
