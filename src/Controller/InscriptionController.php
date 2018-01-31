<?php

    namespace App\Controller;

    use App\Entity\Inscription;
    use App\Form\InscriptionAddType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    class InscriptionController extends Controller
    {
        /**
         * @Route ("/inscription", name="inscription_waitlist")
         */
        public function inscriptionForm(Request $request, \Swift_Mailer $mailer)
        {
            $inscription = new Inscription();
            $inscription->setStatus('waitlisted');

            $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
            //$inscription->setDateInscription($date->format('d-m-Y'));
            $inscription->setDateInscription($date);

            $form = $this->createForm(InscriptionAddType::class, $inscription);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $personneWaitList = $form->getData();
                $repository = $this->getDoctrine()->getRepository(Inscription::class);

                // Si l'adresse mail existe déjà en bdd
                if(($personneBdd = $repository->findOneBy(['email' => $personneWaitList->getEmail()])) != null){
                    return $this->render('formWaitList/inscription-form.html.twig', [
                        'mailExist' => "Adresse mail déjà utilisée",
                        'formWaitList' => $form->createView(),
                    ]);
                }

                else {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($personneWaitList);
                    $em->flush();

                    $message = new \Swift_Message('NumericALL - Confirmation inscription liste d\'attente');
                    $message
                        ->setFrom('helloworldwf3@gmail.com')
                        ->setBody($this->renderView('email/body-confirmation.html.twig', [
                            'prenom' => $personneWaitList->getFirstName(),
                            'nom' => $personneWaitList->getLastName(),
                        ]), 'text/html')
                        ->setTo($personneWaitList->getEmail());

                    $mailer->send($message);
                    $formValid = 'exist';
                    return $this->render('formWaitList/inscription-form.html.twig', [
                        'formWaitList' => $form->createView(),
                        'formValid' => $formValid,
                    ]);

                    //Si on veut rediriger vers une autre route
                    // return $this->redirectToRoute('confirmation_inscription_waitlist');
                }
            }

            return $this->render('formWaitList/inscription-form.html.twig', ['formWaitList' => $form->createView()]);
        }

        /**
         * @Route ("/inscription/confirmation", name="confirmation_inscription_waitlist")
         */
        public function inscriptionConfirmation()
        {
         /*   $repository = $this->getDoctrine()->getRepository(Inscription::class);
            $waitlist = $repository->findBy(['status' => 'waitlisted']);

            $message = new \Swift_Message('Confirmation inscription liste d\'attente');
            $message
                ->setFrom('helloworldwf3@gmail.com') // Expéditeur
                ->setBody($this->renderView('email/body-confirmation.html.twig'), 'text/html');// Contenu

            foreach($waitlist as $personne){
                $message->setTo($personne->getEmail()); // Destinataire
                $mailer->send($message);
            }*/
            //dump($waitlist);

            return $this->render('formWaitList/inscription-confirmation.html.twig');
           /* return $this->render('formWaitList/inscription-confirmation.html.twig', [
                'list' => $waitlist,
            ]);*/
        }
    }