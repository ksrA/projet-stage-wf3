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
                    'label' => ' ',
                    'placeholder' => 'Choisir le lieu de formation',
                    'choices' => [
                        'Piennes' => 'Piennes',
                        'Esch-sur-Alzette' => 'Esch-sur-Alzette',
                    ],
                ])
                ->add('save', SubmitType::class, ['label' => 'Selectionner'])
                ->getForm();

            $form->handleRequest($request);

            //Si le formulaire est soumis
            //On recupérè la liste des candidats en fonction de la locality
            //Avec un appel method du fichier dans le dossier reposiroty
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $waitingList = $this->getDoctrine()->getRepository(Inscription::class)->selectByStatusWaitListed($data['locality']);

                $session = new Session();
                $session->set('list', $waitingList);
                $session->set('campus', $data['locality']);

                return $this->redirectToRoute('create-reunion');
            }
            return $this->render('backOffice/reunion/select-waiting-list.html.twig', [
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
            
            //Récupération de la liste des candidats mis en session
            //Ainsi que de la locality
            //Cf controller selectList
            $waitinglist = $this->get('session')->get('list');
            $campus = $this->get('session')->get('campus');

            $form = $this->createForm(CreateReunionFormType::class);

            // dump($waitingList);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                //Création d'un objet de type SessionFormation
                //Et insertion des données dans l'entité

                $sessionFormation = new SessionFormation();

                $sessionFormation->setCampus($campus);
                $sessionFormation->setDateReunion($data['dateReunion']);
                $sessionFormation->setDateSession($data['dateSession']);
                $sessionFormation->setFinancer($data['financer']);

                $repository = $this->getDoctrine()->getRepository(User::class);
                $user = $repository->find(1);

                //Génération du hash_link qui servira de slug dans l'url du formulaire de candiature
                $bytes = openssl_random_pseudo_bytes(30);
                $hashLink = bin2hex($bytes);

                $sessionFormation->setHashLink($hashLink);

                $em = $this->getDoctrine()->getManager();
                $em->persist($sessionFormation);
                $em->flush();

                //Génération de l'url absolue
                $url = $this->generateUrl(
                    'application_form',
                    array('slug' => $sessionFormation->getHashLink()),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                //Elaboration du message avec le hashlink
                $message = new \Swift_Message('Formulaire de candidature');
                $message
                    ->setFrom('helloworldwf3@gmail.com') // Expéditeur
                    ->setBody($this->renderView('backOffice/email/body-application-link.html.twig',[
                        'campus' => $sessionFormation->getCampus(),
                        'dateReunion' => $sessionFormation->getDateReunion()->format('d/m/Y à H:m'),
                        'dateSession' => $sessionFormation->getDateSession()->format('d/m/Y'),
                        'financer' => $sessionFormation->getFinancer(),
                        'address' => $data['place'],
                        'url' => $url,
                    ]), 'text/html');// Contenu

                //Récupération de la liste des personnes en attente en fonction du campus
                $waitingList = $this->getDoctrine()->getRepository(Inscription::class)->selectByStatusWaitListed($campus);

                //Email envoyé a chaque personne et status modifié
                foreach($waitingList as $personne){
                    $message->setTo($personne->getEmail()); // Destinataire
                    $mailer->send($message);
                    $personne->setStatus('done');
                }
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $submit = "existe";
                return $this->render('backOffice/reunion/create-reunion.html.twig', [
                    'submit' => $submit,
                ]);
            }

            return $this->render('backOffice/reunion/create-reunion.html.twig', [
                'waitinglist' => $waitinglist,
                'campus' => $campus,
                'formCreateReunion' => $form->createView(),
            ]);
        }
    }