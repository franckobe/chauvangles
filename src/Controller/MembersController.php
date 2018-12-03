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
use JWT\Authentication\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class MembersController extends AbstractController
{
    /**
     * @Route("/restapi/members/get-all", name="members_all")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function members_all(SerializerInterface $serializer): Response
    {
        $controller_name = (string) null;
        $code = (string) null;
        $description = (string) null;
        $request_token = null;
        $jsonDecode = (string) null;

        //Get User request
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON

        if($request_json){
            foreach ($request_json as $key => $value){
                if ($key === 'token') {
                    $request_token = $value;
                }
            }
        }

        if($request_token !== null && $request_token === $this->getUser()->getApiToken()) {
            $controller_name = 'members';
            $code = 'T0004';
            $description = 'Liste des utilisateurs inscrits';

            //-------------FETCH RESULTs---------------------------------------------------
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findAll();

            //SEND THE RESPONSE --------------------------------------------------
            $json = $serializer->serialize(
                $users,
                'json', array('groups' => array('group1', 'group2'))
            );
            $jsonDecode = json_decode($json);

        }else {
            $controller_name='error';
            $code = 'E0003';
            $description='Bail non renouvelable';
        }

        //Mise en forme du contenu --------
        $resp_content_json = array(
            'type' => $controller_name,
            'code' => $code,
            'description' => $description,
            'payload' => $jsonDecode
        );

        $resp_jwt_json = $this->json(array(
            'jwt'=> JWT::encode($resp_content_json,getenv('APP_SECRET'))
        ));

        return $resp_jwt_json;
    }

    /**
     * @Route("/restapi/members/get-online", name="members_online")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function members_online(): Response
    {
        $controller_name = (string) null;
        $code = (string) null;
        $description = (string) null;
        $request_token = null;

        //Get User request
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON

        if($request_json) {
            foreach ($request_json as $key => $value) {
                if ($key === 'token') {
                    $request_token = $value;
                }
            }
        }

        if($request_token !== null && $request_token === $this->getUser()->getApiToken()) {
            $controller_name = 'members';
            $code = 'T0005';
            $description = 'Liste des utilisateurs connectés';

            //-------------FETCH RESULTs------------------------------------------
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findConnectedUsers();

            //SEND THE RESPONSE --------------------------------------------------
            //Met au bon format
            $resp_data = $this->get('serializer')->serialize($users, 'json', array('groups' => array('group1', 'group2')));
            //Decode string to json
            $resp_payload = json_decode($resp_data);
        }else {
            $controller_name='error';
            $code = 'E0003';
            $description='Bail non renouvelable';
            $resp_payload = [];
        }
        //Mise en forme du contenu ----------------------------------------------
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
}