<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Registration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        $user = $this->getUser();

        return $this->json(array(
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ));
    }

    /**
     * @Route("/logout", name="app_logout")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function logout(): Response
    {
        $user = $this->getUser();
        $user = $user->getUsername();

        $controller_name = "authentication";
        $code = "T0003";
        $description = "$user a été déconnecté";

        return $this->json(array(
                'type' => $controller_name,
                'code' => $code,
                'description' => $description,
            )
        );
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

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView(),
                'controller_name' => $controller_name,
                )
        );
    }
}
