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
     * @Route("/restapi/discussions/get-messages", name="messages_getmessages")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function messages_getmessages(): Response
    {
        $request_token = (string) null;
        $request_discussionId = (string) null;
        $discuss_name_existing = Group::class;
        $request_messageNumber = (integer) null;
        $payload = (array) null;
        $controller_name= (string) null;
        $code = (string) null;
        $description= (string) null;

        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key === 'token') {
                $request_token = $value;
            } else if ($key === 'discussionId') {
                $request_discussionId = $value;
            } else if ($key === 'messageNumber') {
                $request_messageNumber = $value;
            }
        }

          if ($request_discussionId !== null){
            $discuss_name_existing = $this->getDoctrine()
                ->getRepository(Group::class)
                ->findOneBy(['discussionName' => $request_discussionId]);
           }

        //IF token exist and is a valid one
        if($request_token !== null && $request_token === $this->getUser()->getApiToken()) {

            if ($this->getUser()->getGroups()->contains($discuss_name_existing)) {
                $controller_name = 'discussion';
                $code = 'T0006';
                $description = 'Récupération d\'une discussion existante';
                $id_discuss = $discuss_name_existing->getId();

                if ($request_messageNumber !== null && $request_messageNumber !== 0) {
                    $messages_array = $this->getDoctrine()
                        ->getRepository(GroupMessage::class)
                        ->findBy(['group_' => $id_discuss], null, $request_messageNumber);

                    $payload = array(
                        'id' => $request_discussionId,
                        'label' => $request_discussionId,
                        'lastMessages' => $messages_array
                    );
                } else {
                    //  IF MessageNumber NOT DEFINE : RETURN 20 message
                    $messages_array = $this->getDoctrine()
                        ->getRepository(GroupMessage::class)
                        ->findBy(['group_' => $id_discuss], null, 20);
                    $payload = array(
                        'id' => $request_discussionId,
                        'label' => $request_discussionId,
                        'lastMessages' => $messages_array
                    );
                }
            }
            else {
                //  IF USER ACCESS DENIED DISCUSS : E0009
                $controller_name='error';
                $code = 'E0009';
                $description= 'Vous ne pouvez pas réaliser cette opération car la discussion existe pas ou que vous n\'en faites pas partie';
            }
        }

        //CREATE RESPONSE ----------------------------------------------------------------------------------------------------------------------------
        //Met au bon format
        $resp_data = $this->get('serializer')->serialize($payload, 'json', array('groups' => array('group3', 'group4')));
        //Decodage string to json
        $resp_payload = json_decode($resp_data);

        //Mise en forme du contenu --------
        $resp_content_json = array(
            'type' => $controller_name,
            'code' => $code,
            'description' => $description,
            'payload' => $resp_payload
        );

        $resp_jwt_json = $this->json(array(
            'jwt'=> JWT::encode($resp_content_json,getenv('APP_SECRET'))
        ));
        return $resp_jwt_json;
    }


    /**
     * @Route("/restapi/discussions/post-message", name="messages_postmessage")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function messages_postmessage(): Response
    {
        $controller_name = (string) null;
        $code = (string) null;
        $description = (string) null;
        $request_token = (string) null;
        $request_discussionId = (string) null;
        $discuss_name_existing = Group::class;
        $request_message = (string) null;

        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key === 'token') {
                $request_token = $value;
            } else if ($key === 'discussionId') {
                $request_discussionId = $value;
            } else if ($key === 'message') {
                $request_message = $value;
            }
        }

        if ($request_discussionId !== null){
            $discuss_name_existing = $this->getDoctrine()
                ->getRepository(Group::class)
                ->findOneBy(['discussionName' => $request_discussionId]);
        }

        if($request_token !== null && $request_token === $this->getUser()->getApiToken()) {

            if ($this->getUser()->getGroups()->contains($discuss_name_existing)) {
                $manager = $this->getDoctrine()->getManager();
                $date = new \DateTime();
                $group = new GroupMessage();
                $group->setGroup($discuss_name_existing);
                $group->setSender($this->getUser());
                $group->setDateEmission($date);
                $group->setDateReception($date);
                $group->setDateRead($date);
                $group->setContent($request_message);
                $manager->persist($group);
                $manager->flush();

                $controller_name = 'discussion';
                $code = 'T0012';
                $description = 'Message : ' . $request_message . ': enregistré avec succès';
            }
            else
            {
                //  IF USER ACCESS DENIED DISCUSS : E0009
                $controller_name='error';
                $code = 'E0009';
                $description= 'Vous ne pouvez pas réaliser cette opération car la discussion n\'existe pas ou que vous n\'en faites pas partie';
            }
        }


        //CREATE RESPONSE ----------------------------------------------------------------------------------------------------------------------------
        //Mise en forme du contenu --------
        $resp_content_json = array(
            'type' => $controller_name,
            'code' => $code,
            'description' => $description,
        );

        $resp_jwt_json = $this->json(array(
            'jwt'=> JWT::encode($resp_content_json,getenv('APP_SECRET'))
        ));
        return $resp_jwt_json;
    }
}