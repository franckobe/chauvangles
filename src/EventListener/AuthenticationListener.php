<?php

namespace App\EventListener;

use JWT\Authentication\JWT;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

//use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

class AuthenticationListener extends AbstractController
{

    /**
     * @param AuthenticationSuccessEvent $event
     * @return JsonResponse
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): JsonResponse
    {
        $user = $this->getUser();
        $token = $event->getData();
        $user->setApiToken($token['token']);

        $data = $event->getData();
        $response = $event->getResponse();
        $response->headers->add(['authorization' => $data['token']]); // works

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $message = $event->setData([JWT::encode(['type' => 'authentication',
            'code' => 'T0001',
            'description' => 'Vous êtes maintenant connecté',
            'payload' => $token], 'toto')]);

        return $this->json(array('jwt' => $message));

    }

    /**
     * @param AuthenticationFailureEvent $event
     * @return JsonResponse
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): JsonResponse
    {
        //JWTAuthenticationFailureResponse => modifier la fonction set data pour enlever le code HTTP

        $data = JWT::encode([
            'type'  => 'error',
            'code'  => 'E0001',
            'description' => 'Mauvais login ou mot de passe',
        ], 'toto');

        $response = new JWTAuthenticationFailureResponse($data);

        $event->setResponse($response);

    }

    /**
     * @param JWTExpiredEvent $event
     * @return JsonResponse
     */
    public function onJWTExpired(JWTExpiredEvent $event): JsonResponse
    {
        /** @var JWTAuthenticationFailureResponse */
        $data = JWT::encode([
            'type'  => 'error',
            'code'  => 'E0002',
            'description' => 'Session expirée ou inexistante',
        ], 'toto');

        $response = new JWTAuthenticationFailureResponse($data);

        $event->setResponse($response);
    }
}