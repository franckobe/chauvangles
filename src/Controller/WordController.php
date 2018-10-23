<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class WordController extends AbstractController
{
    /**
     * @Route("/word", name="word")
     * @return Response
     */
    public function word()
    {
        return $this->render('home/word.html.mustache', array());
    }
}
