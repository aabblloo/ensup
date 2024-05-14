<?php

namespace AppBundle\Form;

use AppBundle\Entity\Departement;
use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Nom'))
            ->add('username', TextType::class, array('label' => 'Login'))
            ->add('email', EmailType::class, array('required' => false))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmer'),
                'required' => false
            ))
            ->add('isActive', null, array('label' => 'Activer'))
            ->add('groups', EntityType::class, array(
                'label' => 'Choisir le ou les groupes',
                'class' => Group::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->where('g.role NOT LIKE :role ')
                        ->orderBy('g.id', 'ASC')
                        ->setParameter('role', 'ROLE_SUPER_ADMIN');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('departement', EntityType::class, array(
                'label' => 'Département',
                'class' => Departement::class,
                'placeholder' => '',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.nom', 'ASC');
                },
                'choice_label' => 'nom',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            // 'attr' => array('novalidate' => 'novalidate')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }

}
