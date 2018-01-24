<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Regex;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    class DefaultController extends Controller
    {
        /**
         * @Route("/contact", name="contact_page")
         */
        public function contact(Request $request, \Swift_Mailer $mailer)
        {
            $form = $this->createFormBuilder()
                ->add('lastname', TextType::class, [
                    'label' => 'Nom : ',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 2, 'max' => 30]),
                    ],
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom : ',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 2, 'max' => 30]),
                    ],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse mail : ',
                    'constraints' => [
                        new NotBlank(),
                        new Email([
                            "message" => "Veuilliez saisir une adresse mail valide",
                        ]),
                        new Regex('/^[a-zA-Z0-9.!#$%&’*+=?^_`{|}~-]{1,80}@[a-zA-Z0-9-]{1,40}(?:\.[a-zA-Z0-9-]{1,10})*$/'),
                        new Length(['min' => 2, 'max' => 255]),
                    ]])
                ->add('subject', TextType::class, [
                    'label' => 'Sujet : ',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 2, 'max' => 30]),
                    ],
                ])
                ->add('message', TextareaType::class, [
                    'label' => 'Message : ',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 5]),
                    ],
                ])
                ->add('save', SubmitType::class, ['label' => 'Envoyer'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $message = new \Swift_Message($data['subject']);
                $message
                    ->setFrom($data['email'])
                    ->setBody($this->renderView('email/body-contact.html.twig', [
                        'message' => $data['message'],
                        'lastname' => $data['lastname'],
                        'firstname' => $data['firstname'],

                    ]), 'text/html')
                    ->setTo('anicetkesraoui@gmail.com');

                $mailer->send($message);

                $formValid = 'exist';
                return $this->render('formContact/contact.html.twig', [
                    'formContact' => $form->createView(),
                    'formValid' => $formValid,
                    'message' => $data['message'],
                ]);
            }
            return $this->render('formContact/contact.html.twig', array(
                'formContact' => $form->createView(),
            ));
        }
    }