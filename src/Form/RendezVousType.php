<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Prestation;
use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('heure')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'label' => 'Choisissez une catégorie'
            ])
            ->add('prestation', EntityType::class, [
                'class' => Prestation::class,
                'choice_label' => 'name',
                'label' => 'Choisissez de la prestation'
            ])
            ->add('Lieu', ChoiceType::class, [
                'choices'  => [
                    'Domicile' => 'Domicile',
                    'Sur place' => 'Sur Place',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
