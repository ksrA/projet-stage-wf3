<?php

    namespace App\Form;

    use App\Entity\Inscription;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Regex;

    class InscriptionAddType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            // Formulaire d'inscription liste d'attente

            $builder->add('lastname',   TextType::class, [
                'label' => 'Nom : ',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 30]),
                ],
            ]);
            $builder->add('firstname',   TextType::class, [
                'label' => 'Prénom : ',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 30]),
                ],
            ]);
            $builder->add('locality',   ChoiceType::class, [
                'label' => 'Lieu de formation souhaité : ',
                'placeholder' => 'Choisir le lieu',
                'choices' => [
                    'Piennes' => 'Piennes',
                    'Esch-sur-Alzette' => 'Esch-sur-Alzette',
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
                    //possibilité de faire comme ca, a voir selon la view
                    //new Regex(['pattern' => '/^[\w_]*$/', 'message' => 'login.login.only_alpha_underscore'])
                    new Regex('/^(0|\+33|\+32|\+49|\+352)[-.\s]?[0-9]([0-9]){7,8}$/'),
                ],
            ]);
            $builder->add('save', SubmitType::class, ['label' => 'M\'inscrire']);
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Inscription::class,
            ]);
        }
    }