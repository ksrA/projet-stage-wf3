<?php

    namespace App\Controller;

    use App\Entity\SessionFormation;
    use App\Entity\User;
    use App\Entity\Inscription;
    use App\Form\CreateReunionFormType;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Component\Validator\Constraints\Regex;


    class ReunionController extends Controller
    {
        /**
         * @Route("/panel-admin/select-list", name="select_list")
         */
        public function selectList(Request $request)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

            $form = $this->createFormBuilder()
                ->add('locality', ChoiceType::class, [
                    'label' => 'Campus : ',
                    'placeholder' => 'Choisir le lieu de formation',
                    'choices' => [
                        'Piennes' => 'Piennes',
                        'Esch-sur-Alzette' => 'Esch-sur-Alzette',
                    ],
                ])
                ->getForm();

            return $this->render('reunion/select-waiting-list.html.twig', [
                'formSelectWaitingList' => $form->createView(),
            ]);
        }

        /**
         * @Route("/panel-admin/select-list/create-reunion", name="create-reunion")
         */
        public function createReunion(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

            $locality = $request->get('locality');

            $waitingList = $this->getDoctrine()->getRepository(Inscription::class)->selectByStatusWaitListed($locality);

            //$sessionFormation = new SessionFormation();
            $form = $this->createForm(CreateReunionFormType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $sessionFormation = new SessionFormation();

                $sessionFormation->setCampus($locality);
                $sessionFormation->setDateReunion($data['dateReunion']);
                $sessionFormation->setDateSession($data['dateSession']);
                $sessionFormation->setFinancer($data['financer']);

                //$user = new App\Entity\User();
               //$repository = $this->getDoctrine()->getRepository(User::class);
                //obligé d'utiliser User providers de security.yaml ?
                $repository = $this->getDoctrine()->getRepository(User::class);
                $user = $repository->find(1);

                //encodage du hash_link avec le timestamp de la date de réunion (save en bdd)
                /*$str = $encoder->encodePassword($user,  $sessionFormation->getDateReunion()->getTimestamp()); // password_hash
                $str = 'l/k' . $str;
                $hashLink = str_replace('/', '9x', $str);*/
                $bytes = openssl_random_pseudo_bytes(30);
                $hashLink = bin2hex($bytes);

                $sessionFormation->setHashLink($hashLink);

                $em = $this->getDoctrine()->getManager();
                $em->persist($sessionFormation);
                $em->flush();

                $url = $this->generateUrl(
                    'application_form',
                    array('slug' => $sessionFormation->getHashLink()),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                //Envoi du message avec le hashlink
                $message = new \Swift_Message('Formulaire de candidature');
                $message
                   ->setFrom('helloworldwf3@gmail.com') // Expéditeur
                   ->setBody($this->renderView('email/body-application-link.html.twig',[
                       'campus' => $sessionFormation->getCampus(),
                        'dateReunion' => $sessionFormation->getDateReunion()->format('d/m/Y à H:m'),
                        'dateSession' => $sessionFormation->getDateSession()->format('d/m/Y'),
                        'financer' => $sessionFormation->getFinancer(),
                        'address' => $data['place'],
                        'url' => $url,
                    ]), 'text/html');// Contenu

                $waitingList = $this->getDoctrine()->getRepository(Inscription::class)->selectByStatusWaitListed($campus);

                foreach($waitingList as $personne){
                   $message->setTo($personne->getEmail()); // Destinataire
                   $mailer->send($message);
                   $personne->setStatus('done');
                }
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $submit = "existe";
                return $this->render('reunion/create-reunion.html.twig', [
                    'submit' => $submit,
                ]);
            }

            return $this->render('reunion/create-reunion.html.twig', [
                'waitinglist' => $waitingList,
                'formCreateReunion' => $form->createView(),
            ]);
        }
    }