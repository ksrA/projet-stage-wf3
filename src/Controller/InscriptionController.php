<?php

    namespace App\Controller;

    use App\Entity\Actu;
    use App\Entity\Inscription;
    use App\Form\InscriptionAddType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    class InscriptionController extends Controller
    {
        //Formulaire d'inscription sur liste d'attente

        /**
         * @Route ("/inscription", name="inscription_waitlist")
         */
        public function inscriptionForm(Request $request, \Swift_Mailer $mailer)
        {
            $repository = $this->getDoctrine()->getRepository(Actu::class);
            $lastActu = $repository->findTheLastActu();

            //Création d'un objet de type inscription
            $inscription = new Inscription();
            $inscription->setStatus('waitlisted');

            //On récupére la date du moment où la personne s'inscrit sur liste d'attente
            $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

            $inscription->setDateInscription($date);

            $form = $this->createForm(InscriptionAddType::class, $inscription);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $personneWaitList = $form->getData();
                $repository = $this->getDoctrine()->getRepository(Inscription::class);

                // Si l'adresse mail existe déjà en bdd on return le form avec message d'erreur
                if(($personneBdd = $repository->findOneBy(['email' => $personneWaitList->getEmail()])) != null){
                    return $this->render('frontOffice/formWaitList/inscription-form.html.twig', [
                        'mailExist' => "Adresse mail déjà utilisée",
                        'formWaitList' => $form->createView(),
                        'lastActu' => $lastActu,
                    ]);
                }

                else {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($personneWaitList);
                    $em->flush();

                    //Mail
                    $message = new \Swift_Message('NumericALL - Confirmation inscription liste d\'attente');
                    $message
                        ->setFrom('helloworldwf3@gmail.com')
                        ->setBody($this->renderView('backOffice/email/body-confirmation.html.twig', [
                            'prenom' => $personneWaitList->getFirstName(),
                            'nom' => $personneWaitList->getLastName(),
                        ]), 'text/html')
                        ->setTo($personneWaitList->getEmail());

                    $mailer->send($message);
                    $formValid = 'exist';
                    return $this->render('frontOffice/formWaitList/inscription-form.html.twig', [
                        'formWaitList' => $form->createView(),
                        'formValid' => $formValid,
                        'lastActu' => $lastActu,
                    ]);
                }
            }
            return $this->render('frontOffice/formWaitList/inscription-form.html.twig', [
                'formWaitList' => $form->createView(),
                'lastActu' => $lastActu,
            ]);
        }
    }