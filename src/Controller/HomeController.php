<?php

namespace App\Controller;

use Mustache_Engine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;



class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index()
    {
        return $this->render('home/index.html.mustache', array());
    }

    /**
     * @Route("/about", name="about")
     * @return Response
     */
    public function about()
    {
        $offre = 40;
        return $this->render('home/about.html.mustache', [
            'offre' => $offre
        ]);
    }

    /**
     * @Route("/word", name="word")
     * @return Response
     */
    public function word()
    {
        return $this->render('home/word.html.mustache', array());
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function login()
    {
        return $this->render('home/login.html.mustache', array());
    }
}
