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
        $controller_name = "AboutController";
        return $this->render('about/index.html.twig', ["controller_name" => $controller_name]);
    }
}
