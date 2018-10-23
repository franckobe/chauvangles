<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class AboutController extends AbstractController
{
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
}
