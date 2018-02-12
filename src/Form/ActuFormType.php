<?php

namespace App\Form;

use App\Entity\Actu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActuFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Formulaire d'ajout d'actualité

        $builder->add('title',   TextType::class, [
            'label' => 'Titre de l\'article : ',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'max' => 250]),
            ],
        ]);
        $builder->add('date',   DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de l\'article : ',
        ]);
        $builder->add('img', FileType::class, [
            'label' => 'Image de l\'article : ',
            'constraints' => [
                new File(['mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg']])
            ],
        ]);
        $builder->add('content', TextareaType::class, [
            'label' => "Contenu de l'article : ",
        ]);
        $builder->add('save', SubmitType::class, ['label' => 'Publier l\'article']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Actu::class,
        ]);
    }
}