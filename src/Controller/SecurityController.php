<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Registration;
use JWT\Authentication\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        $user = $this->getUser();
//        $user->setApiToken('test');
//
//        return $this->json(array(
//            'type' => "authentication",
//            'code' => 'T0001',
//            'description' => 'Vous êtes maintenant connecté',
//        ));
    }


    public function api()
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }

    /**
     * @Route("/loginout", name="loginout")
     */
    public function logout()
    {
//          $userEmail = $this->getUser()->email;
//          $user = $this->getUser()->apiToken;
        $user = true;
        if ($user){
            $type = "authentication";
            $code = "T0003";
            $description = "L'utilisateur a été déconnecté";
        }else{
            $type = "error";
            $code = "E0003";
            $description = "Bail non renouvelable";
        }

        $resp_content_json = array(
            'type' => $type,
            'code' => $code,
            'description' => $description,
        );

        $resp_jwt = JWT::encode($resp_content_json,'toto');
        $resp_jwt_json = $this->json(array(
            'jwt'=> $resp_jwt
        ));

        return $resp_jwt_json;
    }


    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncode)
    {
        $user = new User();
        $form = $this->createForm(Registration::class, $user);
        $controller_name = "SecurityController";
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password
            $password = $passwordEncode->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            // 4) save the User
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return new Response(sprintf('User %s successfully created', $user->getUsername()));

    }
}
