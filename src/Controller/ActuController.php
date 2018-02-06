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

            return $this->render('formCreateActu/actu-form.html.twig', [
                'change' => 'exist',
            ]);
        }

        return $this->render('formCreateActu/actu-form.html.twig', [
            'formCreateActu' => $form->createView(),
        ]);
    }

    /**
     * @Route("/actualites/page1", name="actu_page_one")
     */
    public function actualitePageOne()
    {
        $repository = $this->getDoctrine()->getRepository(Actu::class);
        $listActu = $repository->findAllByOrderDesc();

        return $this->render('actu-pages/actu-page1.html.twig',[
            'listActu' => $listActu,
        ]);
    }

    /**
     * @Route("/actualites/{id}", name="actu_by_id")
     */
    public function actuById($id)
    {
        $repository = $this->getDoctrine()->getRepository(Actu::class);
        $actu = $repository->findOneBy(['id' => $id]);

        return $this->render('actu-arts/actu-article.html.twig', [
            'actu' => $actu,
        ]);
    }


}