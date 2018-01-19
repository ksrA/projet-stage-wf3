<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/test", name="test_page")
     */
    public function test()
    {
        return $this->render('test.html.twig');
    }
    /**
     * @Route("/numericall-description", name="numericall")
     */
    public function numericall()
    {
        return $this->render('numericall-description.html.twig');
    }
}