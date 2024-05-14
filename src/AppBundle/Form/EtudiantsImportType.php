<?php

namespace AppBundle\Form;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\MyConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantsImportType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
        ->add('file', FileType::class, ['label' => 'Choisissez un fichier Excel', 'required' => true])
        ->add('Importer', SubmitType::class, ['label'=>"Importer", 'attr' => ['class' => 'btn btn-primary mt-3']]);
    }
    
}
