<?php

    namespace App\Controller;

    use App\Entity\Application;
    use App\Entity\SessionFormation;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    class SessionInfoController extends Controller
    {
        /**
         * @Route("/panel-admin/info-session", name="session_info")
         */
        public function infoSession()
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

            //récupération des infos sessions ordonées DESC
            //pour avoir la dernière tout en haut de la page
            $sessions = $this->getDoctrine()->getRepository(SessionFormation::class)->findAllOrderDesc();

            //Récupération de tous les candidats
            $repositorytwo = $this->getDoctrine()->getRepository(Application::class);


            //Création d'un tableau a double dimension,
            //La premiere dimension contient l'id de réunion
            //La deuxieme contient tous les candidats ayant cet id
            $j = 0;
            foreach ($sessions as $infosession){

                //On recupére les candidats ayant l'id de la reunion en cours
                $applicants = $repositorytwo->findBy(['idReunion' => $infosession->getId()]);

                //On met dans la deuxieme dimension les infos de tous ces candidats
                //La première dimension étant l'id de réunion
                foreach($applicants as $applicant) {

                    $tabApplicant[$infosession->getId()][$j] = $applicant;
                    $j++;
                }
            }

            return $this->render('candidatSession/candidat-session.html.twig', [
                'sessions' => $sessions,
                'applicants' => $tabApplicant,
            ]);
        }
    }