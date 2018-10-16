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
        $debug = array (
            'id' => 5,
            'titre' => "Hey"
        );

        dump($debug);

        return new Response('<html><body>Welcome on slacklite.com</body></html>');

//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/HomeController.php',
//        ]);
    }

    /**
     * @Route("/dir", name="dirnote")
     * @return Mustache_Engine
     */
    public function dirnote()
    {
        $name = 'thomas';
        $value= 1000;
//        $m = new Mustache_Engine
//        echo $m->render('index.html.mustache', array('planet' => 'World!'));

        return $this->render('home/index.html.mustache', array('name' => $name, 'value' => $value));
    }
}
