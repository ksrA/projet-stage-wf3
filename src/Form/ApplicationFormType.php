<?php

namespace App\Form;

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ApplicationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Formulaire de candiature

        $builder->add('firstname',   TextType::class, [
            'label' => 'Prénom : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'max' => 30]),
            ],
        ]);
        $builder->add('lastname',   TextType::class, [
            'label' => 'Nom : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'max' => 30]),
            ],
        ]);
        $builder->add('nationality',   TextType::class, [
            'label' => 'Nationalité : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'max' => 30]),
            ],
        ]);
        $builder->add('nationality',   ChoiceType::class, [
            'label' => 'Nationalité : ',
            'placeholder' => 'Selectionnez votre nationalité',
            'choices' => [
                'Française' => 'Française',
                'Luxembourgeoise' => 'Luxembourgeoise',
                "Autre" => "Autre",
            ],
        ]);
        $builder->add('email',   EmailType::class, [
            'label' => 'Adresse mail : ',
            'constraints' => [
                new NotBlank(),
                new Email([
                    "message" => "Veuilliez saisir une adresse mail valide",
                ]),
                new Regex('/^[a-zA-Z0-9.!#$%&’*+=?^_`{|}~-]{1,80}@[a-zA-Z0-9-]{1,40}(?:\.[a-zA-Z0-9-]{1,10})*$/'),
                new Length(['min' => 2, 'max' => 255]),
            ],
        ]);
        $builder->add('phonenumber',   TextType::class, [
            'label' => 'Téléphone : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 10, 'max' => 14]),
                new Regex('/^(0|\+33|\+32|\+49|\+352)[-.\s]?[0-9]([0-9]){7,8}$/'),
            ],
        ]);
        $builder->add('birthday',   BirthdayType::class, [
            'label' => "Date de naissance",
            'placeholder' => [
                'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
            ]
        ]);
        $builder->add('picture', FileType::class, [
            'label' => 'Photo personnelle(facultatif - format jpeg/png/jpg) : ',
            'constraints' => [
                new File(['mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg']]),
            ],
        ]);
        $builder->add('resume', FileType::class, [
            'label' => 'CV (Format PDF uniquement) : ',
            'constraints' => [
                new NotBlank(),
                new File(['mimeTypes' => ['application/pdf']])
                ],
        ]);
        $builder->add('coverLetter', FileType::class, [
            'label' => 'Lettre de motivation (Format PDF uniquement) : ',
            'constraints' => [
                new NotBlank(),
                new File(['mimeTypes' => ['application/pdf']])
            ],
        ]);
        $builder->add('CityPoleEmploi',   TextType::class, [
            'label' => 'Ville de votre pole emploi : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'max' => 30]),
            ],
        ]);
        $builder->add('situation',   TextType::class, [
            'label' => 'Situation (Etudiant, en reconversion..) : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'max' => 30]),
            ],
        ]);
        $builder->add('yearOfLastDegree',   NumberType::class, [
            'label' => 'Année du dernier diplôme : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 4, 'max' => 4]),
            ],
        ]);
        $builder->add('lastFormation',   TextType::class, [
            'label' => 'Nom du dernier diplôme obtenu : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 1, 'max' => 255]),
            ],
        ]);
        $builder->add('save', SubmitType::class, ['label' => 'Candidater']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}