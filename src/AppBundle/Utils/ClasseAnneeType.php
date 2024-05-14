<?php

namespace AppBundle\Utils;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Specialite;
use AppBundle\Entity\Ue;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\ClasseType;
use AppBundle\Utils\ClasseAnnee;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ClasseAnneeType extends AbstractType
{
    private $securiy;

    public function __construct(Security $security)
    {
        $this->securiy = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('classe', ClasseType::class,[
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => "Niveau d'étude"]
            ])
            ->add('annee', AnneeType::class, [
                'attr' => ['class' => 'chosen-select', 'data-placeholder' => 'Année académique']
            ])

            // ->add('specialite', EntityType::class, [
            //     'label' => 'Spécialité',
            //     'class' => Specialite::class,
            //     'choice_value' => 'id',
            //     'choice_label' => 'nom',
            //     'placeholder' => '',
            //     'attr' => [
            //         'class' => 'chosen-select',
            //         'data-placeholder' => 'Spécialité',
            //     ],
            // ])
            // ->add('lettre', ChoiceType::class, [
            //     'choices' => EtudiantClasse::getLettres(),
            //     'choice_label' => function ($choice) {
            //         return $choice;
            //     },
            //     'placeholder' => '',
            //     'required' => false,
            //     'attr' => [
            //         'class' => 'chosen-select',
            //         'data-placeholder' => MyConfig::CHOSEN_TEXT,
            //     ],
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClasseAnnee::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }

}
