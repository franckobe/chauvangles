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
        $controller_name="error";
        $error = "E0004";
        $description_error="Une discussion doit décrire des membres";
        $error2 = "E0005";
        $description_error2="Trop de members tuent les membres";

        $controller_name = "discussion";
        $code = "T0006";
        $description = "Récupération d'une discussion existante";

        $code2 = "T0007";
        $description2 = "Création d'une discussion";


        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //vérifie si une discussion ayant le même nom et le même créateur existe
        //  IF DISCUSSION_NAME existe : IF MEMBERs IS DEFINE : GET THE DISCUSSION (RETURN T0006)
        //  IF DISCUSSION_NAME existe pas : IF MEMBERS NOT DEFINE : RETURN ERREUR E0004 no member list
        //  IF DISCUSSION_NAME existe pas : IF MEMBERS IS DEFINE (+ de 9 membre) : RETURN ERREUR E0004 too much poeple
        //  IF DISCUSSION_NAME existe pas : SINON la discussion est créée et les membres ajoutés : RETURN T0007

        $payload = array(
            'id' => 'userId as String',
            'login' => 'userLogin as String',
            'status' => 'connected',
        );

        //mettre à jour la liste des membres avant de retourner la réponse
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
        $controller_name="error";
        $error2 = "E0005";
        $description_error2="Trop de members tuent les membres";
        $error = "E0006";
        $description_error="Vous n'avez pas le droit d'effectuer cette manipulation pour cette discussion";

        $controller_name = "discussion";
        $code = "T0008";
        $description = "Membre(s) ajouté(s) avec succès";

        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //  IF USER IS CREATEUR DISCUSSION / IF NOT : RETURN E006 not discussion creator
        //  IF USERS NUMBER < 9 : AJOUT DES MEMBRES + RETURN T0008
        //  IF USERS NUMBER + createur > 9 : RETURN E0005 too much people

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
        $controller_name="error";
        $error = "E0007";
        $description_error="Pour quitter une conversation dont vous êtes créateur, il faut forcer sa suppression";
        $error2 = "E0008";
        $description_error2="Vous ne pouvez quitter cette conversation car vous n'en faites par partie ou qu'elle n'existe pas";

        $controller_name = "discussion";
        $code = "T0009";
        $code2 = "T0010";
        $description = "La discussion a été supprimée, ainsi son historique";
        $description2 = "Vous avez quitté la conversation";

        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //  IF USER IS CREATOR OF DISCUSS
        //      IF force = true : User Leave + Conv delete + message delete : RETURN T0009
        //      IF NOT : RETURN E0007
        //  IF USER IS MEMBER OF DISCUSS
        //      enleve utilisateur de la discussion : RETURN T0010
        //  IF NOT : RETURN E0008

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

        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //  RETURN TABLEAU DISCUSSION Where user is, en disant member or creator


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
        $controller_name="error";
        $error = "E0009";
        $description_error="Vous ne pouvez pas réaliser cette opération car la discussion n'existe pas ou que vous n'en faites pas partie";

        $controller_name = "discussion";
        $code = "T0006";
        $description = "Récupération d'une discussion existante";

        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //  IF USER ACCESS DENIED DISCUSS: RETURN E0009
        //  IF MessageNumber NOT DEFINE : RETURN 30 ou 50 message
        //  RETURN MESSAGE LIST with same format GET_OR_CREATE T0006

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
        $controller_name="error";
        $error = "E0009";
        $description_error="Vous ne pouvez pas réaliser cette opération car la discussion n'existe pas ou que vous n'en faites pas partie";

        $controller_name = "discussion";
        $code = "T0012";
        $description = "Liste des utilisateurs connectés";

        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //  IF USER ACCESS DENIED DISCUSS : E0009
        //  ENREGISTRER MESSAGE POUR DISCUSS AVEC : LOGIN USER ID / DATE HEURE etc..
        //  RETURN T0012

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