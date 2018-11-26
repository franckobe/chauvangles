<?php
/**
 * Created by PhpStorm.
 * User: Garcia D
 * Date: 08/11/2018
 * Time: 18:01
 */

namespace App\Controller;

use JWT\Authentication\JWT;
use App\Entity\User;
use App\Entity\Group;
use App\Entity\GroupMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class DiscussionsController extends AbstractController
{
    /**
     * @return array of message
     */
    public function getLastMessages($request_discussionName)
    {
        $discuss_name_existing = $this->getDoctrine()
            ->getRepository(GroupMessage::class)
            ->findOneBy(['discussionName' => $request_discussionName]);

        return array();
    }

    /**
     * @Route("/discussions/get-or-create", name="discussions_getcreate")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_getcreate(): Response
    {
        $request_discussionName= '';
        $request_members = [];
        $discussionWithoutDiscussionNameHaveSameMembersId = null;
        $discussionWithoutDiscussionNameHaveSameMembers = false;
        $discussionsWithSameMembers = Group::class;
        $discuss_name_existing = Group::class;

        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key == "token") {
                $request_token = $value;
            } else if ($key == "discussionName") {
                $request_discussionName = $value;
            } else if ($key == "members") {
                $request_members = $value;
            }
        }

        //vérifie si une discussion ayant le même nom et le même créateur existe
        if (isset($request_discussionName)){
            $discuss_name_existing = $this->getDoctrine()
                ->getRepository(Group::class)
                ->findOneBy(['discussionName' => $request_discussionName, 'creator' => $this->getUser()->getId()]);
        }

        //SI ON A PAS DE DISCUSSION NAME DANS LA REQUETE CLIENT
        if (($request_discussionName) == null){
            //Vérifie si une discussion avec les mêmes membres éxiste dans le cas ou il n'y a pas de discussion_name
            $discussions = $this->getDoctrine()
                ->getRepository(Group::class)
                ->findAll();

            $userId = [];
            foreach ($discussions as $discussion){
                $users = $discussion->getUsers();
                foreach ($users as $user){
                    array_push($userId, $user->id);
                }
                if (array_diff($userId,$request_members) == array_diff($request_members,$userId)) {
                    $discussionWithoutDiscussionNameHaveSameMembersId = $discussion->getId();
                    $discussionWithoutDiscussionNameHaveSameMembers = true;
                }
                $userId = array();
            }

            $discussionsWithSameMembers = $this->getDoctrine()
                ->getRepository(Group::class)
                ->find($discussionWithoutDiscussionNameHaveSameMembersId);
        }

        //SI LA DISCUSSION N'EXISTE PAS
        if (empty($discuss_name_existing))
        {
            //  IF MEMBERS NOT DEFINE : RETURN ERREUR E0004 no member list
            if (!$request_members)
            {
                $controller_name="error";
                $code = "E0004";
                $description="Une discussion doit décrire des membres";
                $payload = "";
            }
            else if ($request_members)
            {
                //  IF MEMBERS IS DEFINE (+ de 9 membre) : RETURN ERREUR E0004 too much poeple
                $count = count($request_members);
                $users = [];
                foreach ($request_members as $members){
                    array_push($users, $this->getDoctrine()
                        ->getRepository(User::class)
                        ->find($members));

                }

                if ($count >= 9) {
                    $controller_name = "error";
                    $code = "E0005";
                    $description = "Trop de members tuent les membres";
                    $payload = "";
                } else {
                    $manager = $this->getDoctrine()->getManager();
                    $group = new Group();
                    $group->setName($request_discussionName);
                    $group->setCreator($this->getUser()->getId());
                    $group->setDateCreation(new \DateTime());
                    foreach($users as $user){
                        $group->addUser($user);
                        $user->addGroup($group);
                    }
                    $manager->persist($group);
                    $manager->flush();

                    //  SINON la discussion est créée et les membres ajoutés : RETURN T0007
                    $controller_name = "discussion";
                    $code = "T0007";
                    $description = "Création d'une discussion";
                    $payload = array(
                        'id' => $group->getId(),
                        'label' => $request_discussionName
                    );
                }
            }
        }
        //SI LA DISCUSSION EXISTE
        if ($discuss_name_existing && $discussionWithoutDiscussionNameHaveSameMembers == false)
        {
            //  IF DISCUSSION_NAME existe : IF MEMBERs IS DEFINE : GET THE DISCUSSION (RETURN T0006)
            $controller_name = "discussion";
            $code = "T0006";
            $description = "Récupération d'une discussion existante";
//            $messages = $this->getLastMessages($request_discussionName);
            $payload = array(
                'id' => $request_discussionName,
                'label' => $request_discussionName,
//                'lastMessages' => $messages
            );
        }elseif($discussionWithoutDiscussionNameHaveSameMembers){
            //  IF DISCUSSION_NAME existe : IF MEMBERs IS DEFINE : GET THE DISCUSSION (RETURN T0006)
            $controller_name = "discussion";
            $code = "T0006";
            $description = "Récupération d'une discussion existante";
//            $messages = $this->getLastMessages($request_discussionName); // Doit renvoyer des objets group_message
            $payload = array(
                'id' => $discussionsWithSameMembers->getId(),
                'label' => $discussionsWithSameMembers->getName(),
//                'lastMessages' => $messages
            );
        }

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
     * @Route("/discussions/add-member", name="discussions_addmember")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_addmember(): Response
    {
        $request_discussionId = (int) null;
        $request_members = [];

        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key == "token") {
                $request_token = $value;
            } else if ($key == "discussionId") {
                $request_discussionId = $value;
            } else if ($key == "newMembers") {
                $request_members = $value;
            }
        }

        $docRepoUser = $this->getDoctrine()
            ->getRepository(User::class);

        $discuss_name_existing = $this->getDoctrine()
            ->getRepository(Group::class)
            ->findOneBy(['discussionName' => $request_discussionId]);

        //Discussion n'existe pas !
        if (!empty($discuss_name_existing))
        {
            //  IF USER IS CREATEUR DISCUSSION
            if ($this->getUser()->getId() == $discuss_name_existing->getCreator()) {
                //  IF USERS NUMBER < 9 : AJOUT DES MEMBRES + RETURN T0008
                $count = count($request_members);
                $membersAlreadyIn = $discuss_name_existing->getUsers();
                $finalCount = $count + sizeof($membersAlreadyIn);
                if ($finalCount <= 9) {
                    //AJOUTER LE/LES MEMBRES DANS LA BDD
                    $manager = $this->getDoctrine()->getManager();
                    $usersArray = [];
                    $user = new User();
                    foreach($request_members as $members){
                        $user = $docRepoUser->find($members);
                        array_push($usersArray, $user);
                        $discuss_name_existing->addUser($user);
                        $user->addGroup($discuss_name_existing);
                    }

                    $manager->persist($user);
                    $manager->flush();
                    $controller_name = "discussion";
                    $code = "T0008";
                    $description = "Membre(s) ajouté(s) avec succès";
                    $payload = array(
                        'members' => array(
                            "userLogin qui a été ajouté",
                            "userLogin qui a été ajouté"
                        )
                    );
                } else {
                    //  IF USERS NUMBER + createur > 9 : RETURN E0005 too much people
                    $controller_name = "error";
                    $code = "E0005";
                    $description = "Trop de members tuent les membres";
                    $payload = "";
                }
            } else // IF NOT : RETURN E006 not discussion creator
            {
                $controller_name = "error";
                $code = "E0006";
                $description = "Vous n'avez pas le droit d'effectuer cette manipulation pour cette discussion";
            }
        }
        else
        {
            $controller_name = "error";
            $code = "E000?";
            $description = "La discussion n'existe pas ? Comment etes vous arrivez ici vous !?";
            $payload = "";
        }
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
        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key == "token") {
                $request_token = $value;
            } else if ($key == "discussionId") {
                $request_discussionId = $value;
            } else if ($key == "force") {
                $request_force = $value;
            } else {
//                $request_token = "token string";
//                $request_discussionId = "discussId string";
//                $request_force = "force bool";
//                return new Response("La requête n'est pas bien constituée : \"$request_token : $request_discussionId : $request_force\"");
            }
        }
//        return new Response("La requête est bien constituée : \"$request_token : $request_discussionId : $request_force\"");

        $discuss_name_existing = $this->getDoctrine()
            ->getRepository(Group::class)
            ->findOneBy(['discussionName' => $request_discussionId]);

        //CONDITION :
        //  IF SESSIONS TOKEN existe

        //  IF USER IS CREATEUR DISCUSSION
        if ($this->getUser()->getId() == $discuss_name_existing->getCreator())
        {
            if ($request_force == true)
            {
                //      IF force = true : User Leave + Conv delete + message delete : RETURN T0009
                $controller_name = "discussion";
                $code = "T0009";
                $description = "La discussion a été supprimée, ainsi son historique";

                //userCreator Leave -> Delete Discussion + Deletes Messages
            }
            else
            {
                //      IF NOT : RETURN E0007
                $controller_name="error";
                $code = "E0007";
                $description="Pour quitter une conversation dont vous êtes créateur, il faut forcer sa suppression";
            }
        }
        else if ($discuss_name_existing->getUser($this->getUser()->getId()))
        {
            //  IF USER IS MEMBER OF DISCUSS
            //      enleve utilisateur de la discussion : RETURN T0010
            $controller_name = "discussion";
            $code = "T0010";
            $description = "Vous avez quitté la conversation";
        }
        else
        {
            //  IF NOT : RETURN E0008
            $controller_name="error";
            $code = "E0008";
            $description="Vous ne pouvez quitter cette conversation car vous n'en faites par partie ou qu'elle n'existe pas";
        }

        $payload = "";
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