<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

//use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

class AuthenticationListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $event->setData([
            'type' => "authentication",
            'code' => 'T0001',
            'description' => 'Vous êtes maintenant connecté',
            'payload' => $event->getData(),
        ]);
//            'code' => $event->getResponse()->getStatusCode(),
    }

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        //JWTAuthenticationFailureResponse => modifier la fonction set data pour enlever le code HTTP
        $data = [
            'type'  => 'error',
            'code'  => 'E0001',
            'description' => 'Mauvais login ou mot de passe',
        ];

        $response = new JWTAuthenticationFailureResponse($data);

        $event->setResponse($response);

    }


    /**
     * @param JWTExpiredEvent $event
     */
    public function onJWTExpired(JWTExpiredEvent $event)
    {
        /** @var JWTAuthenticationFailureResponse */
        $data = [
            'type'  => 'error',
            'code'  => 'E0002',
            'description' => 'Session expirée ou inexistante',
        ];

        $response = new JWTAuthenticationFailureResponse($data);

        $event->setResponse($response);
    }
}