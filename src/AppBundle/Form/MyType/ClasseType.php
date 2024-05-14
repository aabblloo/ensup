<?php

namespace AppBundle\Form\MyType;

use AppBundle\Entity\Classe;
use AppBundle\Entity\MyConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ClasseType extends AbstractType
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => "Niveau d'étude",
            'class' => Classe::class,
            'choice_value' => 'id',
            'choice_label' => 'nom',

            'query_builder' => function (EntityRepository $er) {
                $query = $er->createQueryBuilder('cl')
                    ->join('cl.cycle', 'cy')
                    ->join('cy.specialite', 'sp')
                    ->join('sp.section', 'sec')
                    ->join('sec.departement', 'dep')
                    ->orderBy('cl.nom', 'ASC');

                if ($this->user->getRolePrincipal() == 'ROLE_DER' or $this->user->getRolePrincipal() == 'ROLE_SAISIE') {
                    $query->where('dep = :departement');
                    $query->setParameter('departement', $this->user->getDepartement());
                }

                return $query;
            },

            'placeholder' => '',
            'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT]
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
