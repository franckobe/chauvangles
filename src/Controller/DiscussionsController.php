<?php
/**
 * Created by PhpStorm.
 * User: Garcia D
 * Date: 08/11/2018
 * Time: 18:01
 */

namespace App\Controller;

use App\Form\Registration;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class DiscussionsController extends AbstractController
{
    /**
     * @Route("/discussions/get-or-create", name="discussions_getcreate")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_getcreate()
    {
        $controller_name = "discussion";
        $code = "T0006";
        $description = "Récupération d'une discussion existante";

        $code = "T0007";
        $description = "Création d'une discussion";

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

    /**
     * @Route("/discussions/add-member", name="discussions_addmember")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_addmember()
    {
        $controller_name = "discussion";
        $code = "T0008";
        $description = "Membre(s) ajouté(s) avec succès";
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

    /**
     * @Route("/discussions/leave", name="discussions_leave")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_leave()
    {
        $controller_name = "discussion";
        $code = "T0009";
        $description = "La discussion a été supprimée, ainsi son historique";
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

    /**
     * @Route("/discussions/list", name="discussions_list")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_list()
    {
        $controller_name = "discussion";
        $code = "T0011";
        $description = "Liste des discussions auxquelles vous prenez part";
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

    /**
     * @Route("/discussions/get-messages", name="discussions_getmessages")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_getmessages()
    {
        $controller_name = "discussion";
        $code = "T0006";
        $description = "Récupération d'une discussion existante";
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


    /**
     * @Route("/discussions/post-message", name="discussions_postmessage")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_postmessage()
    {
        $controller_name = "discussion";
        $code = "T0012";
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