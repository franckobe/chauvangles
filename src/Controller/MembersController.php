<?php
/**
 * Created by PhpStorm.
 * User: Garcia D
 * Date: 08/11/2018
 * Time: 18:04
 */

namespace App\Controller;

use App\Form\Registration;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class MembersController extends AbstractController
{

    /**
     * @Route("/members/get-all", name="members_all")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function members_all(): Response
    {
        $controller_name="error";
        $error = "E0003";
        $description_error="Bail non renouvelable";

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

            //-------------FETCH RESULTs---------------------------------------------------

            $repository = $this->getDoctrine()
                                ->getRepository(User::class)
                                ->findAll();
            $repository = $this->get('serializer')->serialize($repository, 'json');
            $response = new Response($repository);
            $response = json_decode($response->getContent(), JSON_UNESCAPED_SLASHES);

            //SEND THE RESPONSE --------------------------------------------------
            return $this->json(array(
                    'type' => $controller_name,
                    'code' => $code,
                    'description' => $description,
                    'payload' => $response
                )
            );
        }
    }

    /**
     * @Route("/members/get-online", name="members_online")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function members_online(): Response
    {
        $controller_name="error";
        $error = "E0003";
        $description_error="Bail non renouvelable";

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

            //CONDITION :
            //  If TOKEN EXIST
            //  If TOKEN IS VALID
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findByExampleField();

            //-------------FETCH RESULTs---------------------------------------------------
            $repository = $this->get('serializer')->serialize($users, 'json');
            $response = new Response($repository);
            $response = json_decode($response->getContent(), JSON_UNESCAPED_SLASHES);

            //SEND THE RESPONSE --------------------------------------------------
            return $this->json(array(
                    'type' => $controller_name,
                    'code' => $code,
                    'description' => $description,
                    'payload' => $response
                )
            );
        }
    }
}