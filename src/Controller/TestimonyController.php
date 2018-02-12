<?php

namespace App\Controller;

use App\Entity\Testimony;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TestimonyController extends Controller
{
    /**
     * @Route("/testimony", name="testimony")
     */
    public function testimony()
    {
        $repository = $this->getDoctrine()->getRepository(Testimony::class);
        $testimonies = $repository->findAll();

        return $this->render('frontOffice/testimony/testimony.html.twig', [
            'testimonies' => $testimonies,
        ]);
    }

    /**
     * @Route("/panel-admin/testimony", name="panel-admin-testimony")
     */
    public function panelAdminTestimony(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

        $form = $this->createFormBuilder()
            ->add('testimony', TextareaType::class, [
                'label' => 'Témoignage : ',
                'attr' => ['placeholder' => 'Ecrire le témoignage'],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom et prénom : ',
                'attr' => ['placeholder' => 'Ecrire le nom et prénom ou nom de l\'entreprise'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 50]),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $testimony = new Testimony();

            $testimony->setTestimony($data['testimony']);
            $testimony->setName($data['name']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($testimony);
            $em->flush();

            return $this->render('backOffice/testimony/form-add-testimony.html.twig', [
                'change' => 'exist',
            ]);
        }

        return $this->render('backOffice/testimony/form-add-testimony.html.twig', [
            'formTestimony' => $form->createView(),
            'notchange' => 'exist',
        ]);
    }

}