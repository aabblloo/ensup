<?php

namespace AppBundle\Form\MyType;

use AppBundle\Entity\Departement;
use AppBundle\Entity\MyConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class DepartementType extends AbstractType
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => "Département",
            'class' => Departement::class,
            'choice_value' => 'id',
            'choice_label' => 'nom',

            'query_builder' => function (EntityRepository $er) {
                $query = $er->createQueryBuilder('d')
                    ->orderBy('d.nom', 'ASC');

                if ($this->user->getRolePrincipal() == 'ROLE_DER' or $this->user->getRolePrincipal() == 'ROLE_SAISIE') {
                    $query->where('d.id = :id');
                    $query->setParameter('id', $this->user->getDepartement()->getId());
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
