<?php

    namespace App\Controller;


    use App\Entity\Application;
    use App\Entity\SessionFormation;
    use App\Form\ApplicationFormType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Constraints\DateTime;
    use Symfony\Component\HttpFoundation\File\UploadedFile;

    class ApplicationController extends Controller
    {
        /**
         * @Route("/application-form/{slug}", name="application_form")
         */
        public function applicationForm($slug, Request $request, \Swift_Mailer $mailer)
        {
            $repository = $this->getDoctrine()->getRepository(SessionFormation::class);
            if (($sessionFormation = $repository->findOneBy(['hashLink' => $slug])) != null) {
                $hashlink = $sessionFormation->getHashLink();
                $campus = $sessionFormation->getCampus();
                $id = $sessionFormation->getId();
                $dateNow = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
                $dateReunion = $sessionFormation->getDateReunion();

                // Exemple pour explication :
                // echo 'date de maintenant : ' . '<br>';
                // var_dump($dateNow = new \DateTime('now', new \DateTimeZone('Europe/Paris')));
                // echo '<br>' . 'date de la réunion : ';
                // var_dump($dateReunion = $sessionFormation->getDateReunion());
                // $interval = $dateReunion->diff($dateNow);
                // echo $interval->format('%R%a days');

                //Comparaison date
                if ($dateNow > $dateReunion){
                    return new Response('<h1>Candidature close</h1>');
                }

                if ($slug == $hashlink) {

                    //pas besoin de passer application?
                    //$application = new Application(); ?

                    $form = $this->createForm(ApplicationFormType::class);

                    $form->handleRequest($request);

                    if($form->isSubmitted() && $form->isValid()) {
                        $data = $form->getData();

                        //Verifier dans la table candidat que le mail n'existe pas déjà
                        //pour l'id réunion correspondant
                        if (($repository = $this->getDoctrine()->getRepository(Application::class)->checkEmailAndIdReunion($data->getEmail(),$id)) != null){
                            return $this->render('formApplication/application-form.html.twig', [
                                'formApplication' => $form->createView(),
                                'email' => 'existe',
                            ]);
                        }

                        // Déplacement du fichier
                        // $file contient le fichier uploadé, il est de type Symfony\Component\HttpFoundation\File\UploadedFile
                        $cv = $data->getResume();
                        $coverLetter = $data->getCoverLetter();

                        // Génération d'un nom aléatoire
                        $cvName = md5(uniqid()) . '.' . $cv->guessExtension();
                        $coverLetterName = md5(uniqid()) . '.' . $coverLetter->guessExtension();

                        // Déplacement du fichier dans un dossier paramétré à l'avance (service.yaml)
                        $cv->move(
                            $this->getParameter('uploads_directory'),
                            $cvName
                        );

                        $coverLetter->move(
                            $this->getParameter('uploads_directory'),
                            $coverLetterName
                        );

                        // Mise à jour de la table, pour stocker les noms de fichiers (cv lettre motive) et pas le contenu
                        $data->setResume($cvName);
                        $data->setCoverLetter($coverLetterName);

                        $data->setCampus($campus);
                        $data->setIdReunion($id);
                        $data->setStatus('candidat');

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($data);
                        $em->flush();

                        $message = new \Swift_Message('Candidature WF3 envoyée');
                        $message
                            ->setFrom('helloworldwf3@gmail.com') // Expéditeur
                            ->setBody($this->renderView('email/body-application-confirmation.html.twig',[
                            ]), 'text/html');// Contenu
                        $message->setTo($data->getEmail()); // Destinataire
                        $mailer->send($message);

                        return new Response('<h1>ITS WORKING. Candidature envoyée.</h1>');
                    }

                    else{
                        return $this->render('formApplication/application-form.html.twig', [
                           'formApplication' => $form->createView(),
                        ]);
                    }
                }
                else{
                    return new Response('<h1>HASH NON CORRESPONDANT - ERROR 404</h1>');
                }
            }
            return new Response('<h1>ERROR 404</h1>');
        }
    }