<?php

namespace App\Controller;

use App\Entity\Faq;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FaqController extends Controller
{
    /**
     * @Route("/faq", name="faq")
     */
    public function faq()
    {
        $repository = $this->getDoctrine()->getRepository(Faq::class);
        $faqs = $repository->findAll();

        return $this->render('frontOffice/faq/faq.html.twig', [
            'faqs' => $faqs,
        ]);
    }

    /**
     * @Route("/panel-admin/faq", name="panel-admin-faq")
     */
    public function panelAdminFaq(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

        $form = $this->createFormBuilder()
            ->add('question', TextType::class, [
                'label' => 'Question : ',
                'attr' => ['placeholder' => 'Ecrire la nouvelle question'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('response', TextareaType::class, [
                'label' => 'Réponse : ',
                'attr' => ['placeholder' => 'Ecrire la réponse'],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $faq = new Faq();

            $faq->setQuestion($data['question']);
            $faq->setResponse($data['response']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            return $this->render('backOffice/faq/form-add-faq.html.twig', [
                'change' => 'exist',
            ]);
        }

        return $this->render('backOffice/faq/form-add-faq.html.twig', [
            'formFaq' => $form->createView(),
            'notchange' => 'exist',
        ]);
    }

}