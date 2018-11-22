<?php
/**
 * Created by PhpStorm.
 * User: Garcia D
 * Date: 16/11/2018
 * Time: 15:09
 */

namespace App\Controller;

use JWT\Authentication\JWT;
use App\Form\Registration;
use App\Entity\User;
use App\Entity\Group;
use App\Entity\GroupMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MessagesController extends AbstractController
{

    /**
     * @Route("/discussions/get-messages", name="messages_getmessages")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function messages_getmessages(): Response
    {
        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key == "token") {
                $request_token = $value;
            } else if ($key == "discussionId") {
                $request_discussionId = $value;
            } else if ($key == "messageNumber") {
                $request_messageNumber = $value;
            } else {
//                $request_token = "token string";
//                $request_discussionId = "discussionId string";
//                $request_messageNumber = "messageNumber integer";
//                return new Response("La requête n'est pas bien constituée : \"$request_token : $request_discussionId : $request_messageNumber\"");
            }
        }
//        return new Response("La requête est bien constituée : \"$request_token : $request_discussionId : $request_messageNumber\"");

        $controller_name="error";
        $error = "E0009";
        $description_error="Vous ne pouvez pas réaliser cette opération car la discussion n'existe pas ou que vous n'en faites pas partie";

        $controller_name = "discussion";
        $code = "T0006";
        $description = "Récupération d'une discussion existante";

        //CONDITION :
        //  IF SESSIONS TOKEN existe

        //  IF USER ACCESS DENIED DISCUSS: RETURN E0009

        //  IF MessageNumber NOT DEFINE : RETURN 50 message
        //  RETURN MESSAGE LIST with same format GET_OR_CREATE T0006

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
     * @Route("/discussions/post-message", name="messages_postmessage")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function messages_postmessage(): Response
    {
        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key == "token") {
                $request_token = $value;
            } else if ($key == "discussionId") {
                $request_discussionId = $value;
            } else if ($key == "message") {
                $request_message = $value;
            } else {
//                $request_token = "token string";
//                $request_discussionId = "discussionId string";
//                $request_message = "message string";
//                return new Response("La requête n'est pas bien constituée : \"$request_token : $request_discussionId : $request_message\"");
            }
        }
//        return new Response("La requête est bien constituée : \"$request_token : $request_discussionId : $request_message\"");

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

        $payload = $this->messages_getmessages();

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