<?php

namespace AppBundle\Form\MyType;

use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Specialite;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SpecialiteType extends AbstractType
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Spécialité',
            'class' => Specialite::class,

            'query_builder' => function (EntityRepository $er) {
                $query = $er->createQueryBuilder('sp');

                if ($this->user->getRolePrincipal() == 'ROLE_DER' or $this->user->getRolePrincipal() == 'ROLE_SAISIE') {
                    $query->join('sp.section', 'section');
                    $query->where('section.departement = :departement');
                    $query->setParameter('departement', $this->user->getDepartement());
                }

                $query->orderBy('sp.nom', 'ASC');
                return $query;
            },

            'choice_value' => 'id',
            'choice_label' => 'nom',
            'placeholder' => '',
            'attr' => ['class' => 'chosen-select', 'data-placeholder' => MyConfig::CHOSEN_TEXT],
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
