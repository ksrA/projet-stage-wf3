<?php

namespace App\Controller;

use App\Entity\Actu;
use App\Form\ActuFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActuController extends Controller
{

    // Methode qui permet d'enregistrer en bdd des actualités

    /**
     * @Route("/panel-admin/create-actu", name="create_actu")
     */
    public function createActu(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

        $form = $this->createForm(ActuFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Récupération du fichier
            $img = $data->getImg();

            // Génération d'un nom aléatoire
            $imgName = md5(uniqid()) . '.' . $img->guessExtension();

            // Déplacement du fichier dans un dossier paramétré à l'avance (service.yaml)
            $img->move(
                $this->getParameter('uploads_directory_img_actu'),
                $imgName
            );

            // Mise à jour de la table, pour stocker le nom de l'image et pas le contenu
            $data->setImg($imgName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            return $this->render('backOffice/formCreateActu/actu-form.html.twig', [
                'change' => 'exist',
            ]);
        }

        return $this->render('backOffice/formCreateActu/actu-form.html.twig', [
            'formCreateActu' => $form->createView(),
        ]);
    }

    /**
     * @Route("/actualites/list/{page}", name="page_actu")
     */
    public function pagination($page)
    {
        if (!is_numeric($page) || $page < 1) {
           return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }

        $repository = $this->getDoctrine()->getRepository(Actu::class);

        //Récupération des dernières actu pour le footer et aside
        $lastActuFooter = $repository->findTheLastActu();
        $lastActuAside = $repository->findTheLastActu(5);

        //Récupération des actualités de la page demandé
        if (!$actus = $repository->findAllPaginedAndSorted($page, 6)){
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }

        //Récupération du nombre d'entrée en utilisant paginator (instancié avec la requete)
        //Afin de calculer le nombre de page
        $pagination = $repository->findAllPaginedAndSorted($page, 6, 'count');

        $nbPage = ceil(count($pagination) / 6);

        return $this->render('frontOffice/actu-pagination/list-actu.html.twig', [
            'actus' => $actus,
            'page' => $page,
            'nbPage' => $nbPage,
            'lastActu' => $lastActuFooter,
            'lastActuAside' => $lastActuAside,
        ]);
    }

    /**
     * @Route("/actualites/articles/{id}", name="actu_by_id")
     */
    public function actuById($id)
    {
        //Récupération de l'actualité en fonction de son id
        //On récupére aussi l'actu d'après et la précedente
        $repository = $this->getDoctrine()->getRepository(Actu::class);
        $actu = $repository->findOneBy(['id' => $id]);
        $precActu = $repository->findOneBy(['id' => $id - 1]);
        $nextActu = $repository->findOneBy(['id' => $id + 1]);

        //Récupération des dernières actu pour le footer et aside
        $lastActuFooter = $repository->findTheLastActu();
        $lastActuAside = $repository->findTheLastActu(5);

        if ($precActu != null){
            $precactu = 'exist';
        }

        if ($nextActu != null){
            $nextactu = 'exist';
        }

        //S'il n'y a pas d'actu correspondant a cet id on retourne la page 404
        if ($actu == null){
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }
        //Sinon, vérification des actu précédentes et next
        //pour savoir quelle bouton de naviguation afficher en vue
        else{
            if (isset($precactu) && isset($nextactu)){
                return $this->render('frontOffice/actu-arts/actu-article.html.twig', [
                    'actu' => $actu,
                    'precActu' => $precActu,
                    'nextActu' => $nextActu,
                    'lastActu' => $lastActuFooter,
                    'lastActuAside' => $lastActuAside,
                ]);
            }
            elseif (isset($precactu) && !isset($nextactu)){
                return $this->render('frontOffice/actu-arts/actu-article.html.twig', [
                    'actu' => $actu,
                    'precActu' => $precActu,
                    'lastActu' => $lastActuFooter,
                    'lastActuAside' => $lastActuAside,
                ]);
            }
            elseif (!isset($precactu) && isset($nextactu)){
                return $this->render('frontOffice/actu-arts/actu-article.html.twig', [
                    'actu' => $actu,
                    'nextActu' => $nextActu,
                    'lastActu' => $lastActuFooter,
                    'lastActuAside' => $lastActuAside,
                ]);
            }
            else{
                return $this->render('frontOffice/actu-arts/actu-article.html.twig', [
                    'actu' => $actu,
                    'lastActu' => $lastActuFooter,
                    'lastActuAside' => $lastActuAside,
                ]);
            }
        }
    }
}