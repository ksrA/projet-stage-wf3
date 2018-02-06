<?php

    namespace App\Controller;

    use App\Entity\Actu;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Response;

    class Wf3Controller extends Controller
    {
        /**
         * @Route("/description", name="Webforce3_description")
         */
        public function description()
        {
            $repository = $this->getDoctrine()->getRepository(Actu::class);
            $lastActu = $repository->findTheLastActu();

            return $this->render('frontOffice/description_formation/wf3_desc.html.twig', [
                'lastActu' => $lastActu,
            ]);
        }
    }