<?php

    namespace App\Controller;

    use App\Entity\Application;
    use App\Entity\SessionFormation;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Component\Routing\Annotation\Route;

    class AjaxController extends Controller
    {
        /**
         * @Route("/panel-admin/note", name="note_page")
         */
        public function note(Request $request)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

            // On recupère les 10 dernières sessions de reunion
            $SessionFormation = $this->getDoctrine()->getRepository(SessionFormation::class)->findAllOrderDesc();

            //Pour chaque session on place son id de réunion dans un tableau
            foreach ($SessionFormation as $elem){
                $sessionKey[] = ($elem->getId());
            }

            //Pour chaque session, on place sa date de réunion dans un tableau
            foreach ($SessionFormation as $elem) {
                $sessionValue[] = 'Date de réunion : ' . $elem->getDateReunion()->format('d/m/Y');
            }

            //On combine les 2 tableaux, les keys seront les date de réunion et la valeur les id de réunion
            $arraySession = array_combine($sessionValue, $sessionKey);

            //Génération du formulaire select avec en choix les différentes réunion.
            //Lorsqu'on choisi une réunion, on récupère sa valeur qui est son id
            //La suite se passe en ajax (note.js)
            $form = $this->createFormBuilder()
                ->add('select', ChoiceType::class, [
                    'placeholder' => 'Choisir la réunion : ',
                    'choices' => $arraySession,
                ])
                ->getForm();

            return $this->render('note/enter-note.html.twig', [
                'formNote' => $form->createView(),
            ]);
        }

        /**
         * @Route("/panel-admin/note/generate-tab", name="generate_tab")
         */
        public function generateTab(Request $request)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

            //Recupération de l'id de réunion envoyé en ajax
            $id = $request->get('id');

            //On récupére la liste des candidats ayant l'id de reunion
            $repository = $this->getDoctrine()->getRepository(Application::class);

            $listCandidat = $repository->findBy(['idReunion' => $id]);

            //On return ce templates a la requete ajax
            //template qui contient l'affichage du tableau et champs input pour ajouter les notes
            if (!empty($listCandidat)){
                return $this->render('note/tab-candidat-note.html.twig', [
                    'candidats' => $listCandidat,
                    'reunion' => 'existe',
                ]);
            }
            else {
                return $this->render('note/tab-candidat-note.html.twig', [
                    'candidats' => $listCandidat,
                ]);
            }
        }
        
        /**
         * @Route("/panel-admin/note/save-note", name="save_note")
         */
        public function saveNote(Request $request)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');


            //On recupère toutes les données POST suite à la soumission du formulaire dans un tableau associatif
            // cf tab-candidat-note.html.twig
            $postData = $request->request->all();

            $repository = $this->getDoctrine()->getRepository(Application::class);

            //On parcours le tableau
            //Les keys correspondent aux ids des candidats ( cf name={{ candidat.id }} )
            //Les values correspondent aux notes entrées
            foreach($postData as $key => $value){
                //Si le champs note a été remplit on save sa note en bdd et on lui attribut un nouveau status
                if ($value != ""){
                    $candidat = $repository->findOneBy(['id' => $key]);
                    $candidat->setNote((int)$value);
                    $candidat->setStatus('noted');
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('note_page');
        }

        /**
         * @Route("/panel-admin/note/search-candidat-by-note-ref", name="search_candidat_by_note-ref")
         */
        public function searchCandidatByNoteRef(Request $request)
        {
            //Récupération des données envoyées en ajax
            $postData = $request->request->all();

            $noteRef = $postData['noteRef'];
            $id = $postData['id'];

            //Récupération en bdd des candidats ayant l'id de réunion et une note > a celle reçu du formulaire
            $repository = $this->getDoctrine()->getRepository(Application::class);
            $listCandidat = $repository->findByIdAndNote($id, $noteRef);

            // Si on a bien des candidats réunissant les criteres de selection
            if (!empty($listCandidat)){
                return $this->render('note/list-candidat-note-filter.html.twig', [
                    'candidats' => $listCandidat,
                    'reunion' => 'existe',
                ]);
            }
            else {
                return $this->render('note/list-candidat-note-filter.html.twig');
            }
        }
    }
