<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActuController extends Controller
{
    /**
     * @Route("/actualitesp1", name="actu_page1")
     */
    public function actuPage1()
    {
        return $this->render('actu-pages/actu-page1.html.twig');
    }

    /**
     * @Route("/actualitesp2", name="actu_page2")
     */
    public function actuPage2()
    {
        return $this->render('actu-pages/actu-page2.html.twig');
    }



    /**
     * @Route("/art050118", name="article050118_page")
     */
    public function article050118()
    {
        return $this->render('actu-arts/actu-article-05-01-18.html.twig');
    }

    /**
     * @Route("/art081217", name="article081217_page")
     */
    public function article081217()
    {
        return $this->render('actu-arts/actu-article-08-12-17.html.twig');
    }

    /**
     * @Route("/art281117", name="article281117_page")
     */
    public function article281117()
    {
        return $this->render('actu-arts/actu-article-28-11-17.html.twig');
    }

    /**
     * @Route("/art110917", name="article110917_page")
     */
    public function article110917()
    {
        return $this->render('actu-arts/actu-article-11-09-17.html.twig');
    }

    /**
     * @Route("/art070917", name="article070917_page")
     */
    public function article070917()
    {
        return $this->render('actu-arts/actu-article-07-09-17.html.twig');
    }

    /**
     * @Route("/art070917esch", name="article070917esch_page")
     */
    public function article070917esch()
    {
        return $this->render('actu-arts/actu-article-07-09-17esch.html.twig');
    }

    /**
     * @Route("/art110517", name="article110517_page")
     */
    public function article110517()
    {
        return $this->render('actu-arts/actu-article-11-05-17.html.twig');
    }

    /**
     * @Route("/art050117", name="article050117_page")
     */
    public function article050117()
    {
        return $this->render('actu-arts/actu-article-05-01-17.html.twig');
    }



    /**
     * @Route("/actualitesdu0118", name="actus_du_0118_page")
     */
    public function actuDu0118()
    {
        return $this->render('actu-arts-par-mois/actus-du-01-18.html.twig');
    }

    /**
     * @Route("/actualitesdu1217", name="actus_du_1217_page")
     */
    public function actuDu1217()
    {
        return $this->render('actu-arts-par-mois/actus-du-12-17.html.twig');
    }

    /**
     * @Route("/actualitesdu1117", name="actus_du_1117_page")
     */
    public function actuDu1117()
    {
        return $this->render('actu-arts-par-mois/actus-du-11-17.html.twig');
    }

    /**
     * @Route("/actualitesdu0917", name="actus_du_0917_page")
     */
    public function actuDu0917()
    {
        return $this->render('actu-arts-par-mois/actus-du-09-17.html.twig');
    }

    /**
     * @Route("/actualitesdu0517", name="actus_du_0517_page")
     */
    public function actuDu0517()
    {
        return $this->render('actu-arts-par-mois/actus-du-05-17.html.twig');
    }

    /**
     * @Route("/actualitesdu0117", name="actus_du_0117_page")
     */
    public function actuDu0117()
    {
        return $this->render('actu-arts-par-mois/actus-du-01-17.html.twig');
    }





    /**
     * @Route("/test", name="test")
     */
    public function teste()
    {
        return $this->render('test.html.twig');
    }


}