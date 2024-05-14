<?php

namespace AppBundle\Form\MyType;

use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Ue;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UeType extends AbstractType
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => "Unité d'Enseignement",
            'class' => Ue::class,
            'choice_value' => 'id',

            'query_builder' => function (EntityRepository $er) {
                $query = $er->createQueryBuilder('ue')
                    ->join('ue.classe', 'cl')
                    ->join('cl.cycle', 'cy')
                    ->join('cy.specialite', 'sp')
                    ->join('sp.section', 'sec')
                    ->join('sec.departement', 'dep')
                    ->orderBy('ue.nom', 'ASC');

                if ($this->user->getRolePrincipal() == 'ROLE_DER' or $this->user->getRolePrincipal() == 'ROLE_SAISIE') {
                    $query->where('dep = :departement');
                    $query->setParameter('departement', $this->user->getDepartement());
                }

                return $query;
            },

            'choice_label' => 'nomComplet',
            'placeholder' => '',
            'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
