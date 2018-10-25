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
        $controller_name = "WordController"
;        $directorWord = "Le mot du directeur";
        return $this->render('word/index.html.twig', [
            'directorWord' => $directorWord,
            'controller_name' => $controller_name,
        ]);
    }
}
