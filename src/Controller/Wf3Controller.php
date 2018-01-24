<?php

    namespace App\Controller;

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
            return $this->render('description_formation/wf3_desc.html.twig');
        }
    }