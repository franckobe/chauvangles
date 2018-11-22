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
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $user = $this->getUser();
        $token = $event->getData();
        $user->setApiToken($token['token']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $message = $event->setData([JWT::encode(['type' => "authentication",
            'code' => 'T0001',
            'description' => 'Vous êtes maintenant connecté',
            'payload' => $token], 'toto')]);

        return $this->json(array('jwt' => $message));

    }

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
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
     */
    public function onJWTExpired(JWTExpiredEvent $event)
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