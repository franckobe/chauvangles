<?php
/**
 * Created by PhpStorm.
 * User: Garcia D
 * Date: 08/11/2018
 * Time: 18:01
 */

namespace App\Controller;

use JWT\Authentication\JWT;
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
    public function discussions_getcreate(): Response
    {
        //VARIABLE ERREUR -----------------------------------------------------------
        $controller_name="error";
        //ERREUR 1---------------------
        $error = "E0004";
        $description_error="Une discussion doit décrire des membres";
        //ERREUR 2---------------------
        $error2 = "E0005";
        $description_error2="Trop de members tuent les membres";

        //VARIABLE FONCTION ----------------------------------------------------------
        $controller_name = "discussion";
        //FCT 1 -------------------------
        $code = "T0006";
        $description = "Récupération d'une discussion existante";
        //FCT 2 -------------------------
        $code2 = "T0007";
        $description2 = "Création d'une discussion";

        //Recuperation de la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str,true); //object JSON
        foreach ($request_json as $key => $value)
        {
            if($key == "token")
            {
                $request_token = $value;
            }
            else if ($key == "discussionName")
            {
                $request_discussionName = $value;
            }
            else if ($key == "members")
            {
                $request_members = $value;
                foreach ($request_members as $id => $val)
                {
                   // $request_members1 = $val;
                }
            }
            else
            {
                $request_token = "error";
                $request_discussionName = "error";
                $request_members = "error";
                return new Response("La requête n'est pas bien constituée");
            }
        }
//        return new Response($request_token);


        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //vérifie si une discussion ayant le même nom et le même créateur existe
        //  IF DISCUSSION_NAME existe : IF MEMBERs IS DEFINE : GET THE DISCUSSION (RETURN T0006)
        //  IF DISCUSSION_NAME existe pas : IF MEMBERS NOT DEFINE : RETURN ERREUR E0004 no member list
        //  IF DISCUSSION_NAME existe pas : IF MEMBERS IS DEFINE (+ de 9 membre) : RETURN ERREUR E0004 too much poeple
        //  IF DISCUSSION_NAME existe pas : SINON la discussion est créée et les membres ajoutés : RETURN T0007











        $payloadT0006 = array(
            'id' => 'discussionId as StringOrInt',
            'label' => 'discussionLabel as String',
            'status' => 'connected',
            'lastMessages' => array(
                'author' => 'authorLogin as String',
                'message' => 'message as StringOrBase64',
                'dateTime' => 'date as ISODateTime',
            )
        );

        $payloadT0007 = array(
            'id' => 'discussionId as StringOrInt',
            'label' => 'discussionLabel as String'
        );


        //CREATE RESPONSE ----------------------------------------------------------------------------------------------------------------------------
        $resp_data = $this->get('serializer')->serialize($payloadT0006, 'json');                         //Met au bon format
        $resp_payload = json_decode($resp_data);                                                //Decodage string to json

        //Mise en forme du contenu --------
        $resp_content_json = array(
            'type' => $controller_name,
            'code' => $code,
            'description' => $description,
            'payload' => $resp_payload
        );
        $resp_jwt = JWT::encode($resp_content_json,'toto');          //On le met au format JWT
        $resp_jwt_json = $this->json(array(
            'jwt'=> $resp_jwt
        ));                                                         // Creation du JSON contenant jwt: token_jwt
        return $resp_jwt_json;                                     //Envoi du token jwt
    }

    /**
     * @Route("/discussions/add-member", name="discussions_addmember")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_addmember(): Response
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
            'members' => array(
                "userLogin as String",
                "userLogin as String"
            )
        );

        //CREATE RESPONSE ----------------------------------------------------------------------------------------------------------------------------
        $resp_data = $this->get('serializer')->serialize($payload, 'json');                         //Met au bon format
        $resp_payload = json_decode($resp_data);                                                //Decodage string to json

        //Mise en forme du contenu --------
        $resp_content_json = array(
            'type' => $controller_name,
            'code' => $code,
            'description' => $description,
            'payload' => $resp_payload
        );
        $resp_jwt = JWT::encode($resp_content_json,'toto');          //On le met au format JWT
        $resp_jwt_json = $this->json(array(
            'jwt'=> $resp_jwt
        ));                                                         // Creation du JSON contenant jwt: token_jwt

        return $resp_jwt_json;                                     //Envoi du token jwt

    }

    /**
     * @Route("/discussions/leave", name="discussions_leave")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_leave(): Response
    {
        $controller_name="error";
        $error = "E0007";
        $description_error="Pour quitter une conversation dont vous êtes créateur, il faut forcer sa suppression";
        $error2 = "E0008";
        $description_error2="Vous ne pouvez quitter cette conversation car vous n'en faites par partie ou qu'elle n'existe pas";

        $controller_name = "discussion";
        $code = "T0009";
        $description = "La discussion a été supprimée, ainsi son historique";
        $code2 = "T0010";
        $description2 = "Vous avez quitté la conversation";

        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //  IF USER IS CREATOR OF DISCUSS
        //      IF force = true : User Leave + Conv delete + message delete : RETURN T0009
        //      IF NOT : RETURN E0007
        //  IF USER IS MEMBER OF DISCUSS
        //      enleve utilisateur de la discussion : RETURN T0010
        //  IF NOT : RETURN E0008

        $payload = null;

        //CREATE RESPONSE ----------------------------------------------------------------------------------------------------------------------------
        $resp_data = $this->get('serializer')->serialize($payload, 'json');                         //Met au bon format
        $resp_payload = json_decode($resp_data);                                                //Decodage string to json

        //Mise en forme du contenu --------
        $resp_content_json = array(
            'type' => $controller_name,
            'code' => $code,
            'description' => $description,
            'payload' => $resp_payload
        );
        $resp_jwt = JWT::encode($resp_content_json,'toto');          //On le met au format JWT
        $resp_jwt_json = $this->json(array(
            'jwt'=> $resp_jwt
        ));                                                         // Creation du JSON contenant jwt: token_jwt
        return $resp_jwt_json;                                     //Envoi du token jwt
    }

    /**
     * @Route("/discussions/list", name="discussions_list")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_list(): Response
    {
        $controller_name = "discussion";
        $code = "T0011";
        $description = "Liste des discussions auxquelles vous prenez part";

        //CONDITION :
        //  IF SESSIONS TOKEN existe
        //  RETURN TABLEAU DISCUSSION Where user is, en disant member or creator


        $payload = array(
            'status' => 'creator',
            'id' => 'discussionId as String',
            'description' => 'description As String or Empty string'
        );

        //CREATE RESPONSE ----------------------------------------------------------------------------------------------------------------------------
        $resp_data = $this->get('serializer')->serialize($payload, 'json');                         //Met au bon format
        $resp_payload = json_decode($resp_data);                                                //Decodage string to json

        //Mise en forme du contenu --------
        $resp_content_json = array(
            'type' => $controller_name,
            'code' => $code,
            'description' => $description,
            'payload' => $resp_payload
        );
        $resp_jwt = JWT::encode($resp_content_json,'toto');          //On le met au format JWT
        $resp_jwt_json = $this->json(array(
            'jwt'=> $resp_jwt
        ));                                                         // Creation du JSON contenant jwt: token_jwt
        return $resp_jwt_json;                                     //Envoi du token jwt
    }

}