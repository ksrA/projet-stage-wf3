<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateReunionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Formulaire de création réunion

            $builder->add('dateReunion',   DateTimeType::class, [
                'label' => 'Date réunion : ',
                'placeholder' => array(
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                    'hour' => 'Heure', 'minute' => 'Minute',
                ),
            ]);
            $builder->add('place',   TextType::class, [
                'label' => 'Adresse réunion : ',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ]);
            $builder->add('financer',   TextType::class, [
                'label' => 'Financeur : ',
                'constraints' => [
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ]);
            $builder->add('dateSession',   DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date début formation : ',
            ]);
            $builder->add('save', SubmitType::class, [
                'label' => 'Créer la réunion'
            ]);
    }
}