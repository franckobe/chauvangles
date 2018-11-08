<?php

namespace App\Controller;

use App\Form\Registration;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncode)
    {
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {

            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig',
                ['last_username' => $lastUsername,
                    'error' => $error]);
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $user = $this->getUser();
            $username = $user->getUsername();
            $controller_name = "IndexController";

        return $this->json(array('user' => $user));
//        return $this->render(
//            'home/home.html.twig',
//            array('username' => $username,
//                'controller_name' => $controller_name)
//        );
    }

    /**
     * @Route("/members/get-all", name="members_all")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function members_all()
    {
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig',
                ['last_username' => $lastUsername,
                    'error' => $error]);
        }
        else
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $controller_name = "members";
            $code = "T0004";
            $description = "Liste des utilisateurs inscrits";
            $payload = array(
                'id' => 'userId as String',
                'login' => 'userLogin as String',
                'status' => 'disconnected',
            );

            return $this->json(array(
                    'type' => $controller_name,
                    'code' => $code,
                    'description' => $description,
                    'payload' => $payload
                )
            );
        }
    }

    /**
     * @Route("/members/get-online", name="members_online")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function members_online()
    {
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig',
                ['last_username' => $lastUsername,
                    'error' => $error]);
        }
        else
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $controller_name = "members";
            $code = "T0005";
            $description = "Liste des utilisateurs connectés";
            $payload = array(
                'id' => 'userId as String',
                'login' => 'userLogin as String',
                'status' => 'connected',
            );

            return $this->json(array(
                    'type' => $controller_name,
                    'code' => $code,
                    'description' => $description,
                    'payload' => $payload
                )
            );
        }
    }
}