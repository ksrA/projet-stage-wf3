<?php
namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class NumericallController extends Controller
{
    /**
     * @Route("/numericall", name="numericall_desc")
     */
    public function numericall_desc()

    {

        return $this->render('numericall.html.twig');

    }
}