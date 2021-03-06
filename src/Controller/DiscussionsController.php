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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @Route("/restapi/discussions/get-or-create", name="discussions_getcreate")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_getcreate(): Response
    {

        $request_discussionName = (string) null;
        $request_members = [];
        $discussionWithoutDiscussionNameHaveSameMembersId = null;
        $discussionWithoutDiscussionNameHaveSameMembers = false;
        $discussionsWithSameMembers = Group::class;
        $discuss_name_existing = Group::class;
        $users = array();
        $controller_name = (string) null;
        $description = (string) null;
        $code = (string) null;
        $payload = 'ya pas token';

        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key === 'token') {
                $request_token = $value;
            } else if ($key === 'discussionName') {
                $request_discussionName = $value;
            } else if ($key === 'members') {
                $request_members = $value;
            }
        }

        if($request_token !== null && $request_token === $this->getUser()->getApiToken()){

        //vérifie si une discussion ayant le même nom et le même créateur existe
        if ($request_discussionName !== null){
            $discuss_name_existing = $this->getDoctrine()
                ->getRepository(Group::class)
                ->findOneBy(['discussionName' => $request_discussionName, 'creator' => $this->getUser()->getId()]);
        }

        //SI ON A PAS DE DISCUSSION NAME DANS LA REQUETE CLIENT

        if (empty($request_discussionName)){
            //Vérifie si une discussion avec les mêmes membres éxiste dans le cas ou il n'y a pas de discussion_name
            $discussions = $this->getDoctrine()
                ->getRepository(Group::class)
                ->findAll();

            $userId = [];
            foreach ($discussions as $discussion){
                $users = $discussion->getUsers();

                foreach ($users as $user){
                    $userId[] =  $user->getId();
                }

                if (array_diff($userId,$request_members) === array_diff($request_members,$userId)) {
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
                $controller_name='error';
                $code = 'E0004';
                $description='Une discussion doit décrire des membres';
                $payload = '';
            }
            else if ($request_members)
            {
                //  IF MEMBERS IS DEFINE (+ de 9 membre) : RETURN ERREUR E0004 too much poeple
                $count = \count($request_members);

                foreach ($request_members as $members){
                    $users[] = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->find($members);
                }
                $users[] = $this->getUser(); //Add the current user (creator) in the discussion
                if ($count >= 9) {
                    $controller_name = 'error';
                    $code = 'E0005';
                    $description = 'Trop de members tuent les membres';
                    $payload = '';
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
                    $controller_name = 'discussion';
                    $code = 'T0007';
                    $description = 'Création d\'une discussion';
                    $payload = array(
                        'id' => $group->getId(),
                        'label' => $request_discussionName
                    );
                }
            }
        }
        //SI LA DISCUSSION EXISTE
            if ($discuss_name_existing !== null && $discussionWithoutDiscussionNameHaveSameMembers === false)
            {
                //  IF DISCUSSION_NAME existe : IF MEMBERs IS DEFINE : GET THE DISCUSSION (RETURN T0006)
                $controller_name = 'discussion';
                $code = 'T0006';
                $description = 'Récupération d\'une discussion existante';
                $id_discuss = $discuss_name_existing->getId();

                // Get all messages with the given discussion ID
                $messages_array = $this->getDoctrine()
                    ->getRepository(GroupMessage::class)
                    ->findBy(['group_' => $id_discuss], null, 20);

                //Make a payload with all needed informations
                $payload = array(
                    'id' => $request_discussionName,
                    'label' => $request_discussionName,
                    'lastMessages' => $messages_array
                );
            }elseif($discussionWithoutDiscussionNameHaveSameMembers){
                //  IF DISCUSSION_NAME existe : IF MEMBERs IS DEFINE : GET THE DISCUSSION (RETURN T0006)
                $controller_name = 'discussion';
                $code = 'T0006';
                $description = 'Récupération d\'une discussion existante';

                $messages_array = $this->getDoctrine()
                    ->getRepository(GroupMessage::class)
                    ->findBy(['group_' => $discussionWithoutDiscussionNameHaveSameMembersId], null, 20);

                $payload = array(
                    'id' => $discussionsWithSameMembers->getId(),
                    'label' => $discussionsWithSameMembers->getName(),
                    'lastMessages' => $messages_array
                );
            }
        }

        //CREATE RESPONSE ----------------------------------------------------------------------------------------------------------------------------
        $resp_data = $this->get('serializer')->serialize($payload, 'json',array('groups' => array('group3', 'group4')) );                         //Met au bon format
        $resp_payload = json_decode($resp_data);                                                //Decodage string to json

        //Mise en forme du contenu --------
        $resp_content_json = array(
            'type' => $controller_name,
            'code' => $code,
            'description' => $description,
            'payload' => $resp_payload
        );
        $resp_jwt_json = $this->json(array(
            'jwt'=> JWT::encode($resp_content_json,getenv('APP_SECRET'))
        ));                                                         // Creation du JSON contenant jwt: token_jwt
        return $resp_jwt_json;                                     //Envoi du token jwt

    }

    /**
     * @Route("/restapi/discussions/add-member", name="discussions_addmember")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_addmember(): Response
    {
        $request_discussionId = (int) null;
        $request_members = [];
        $controller_name = (string) null;
        $code = (string) null;
        $description = (string) null;
        $payload = (string) null;
        $request_token = null;

        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON

        foreach ($request_json as $key => $value){
            if ($key === 'token') {
                $request_token = $value;
            } else if ($key === 'discussionId') {
                $request_discussionId = $value;
            } else if ($key === 'newMembers') {
                $request_members = $value;
            }
        }

        if($request_token !== null && $request_token === $this->getUser()->getApiToken()) {

            $docRepoUser = $this->getDoctrine()
                ->getRepository(User::class);

            $discuss_name_existing = $this->getDoctrine()
                ->getRepository(Group::class)
                ->findOneBy(['discussionName' => $request_discussionId]);

            //Discussion n'existe pas !
            if (!empty($discuss_name_existing)) {
                //  IF USER IS CREATEUR DISCUSSION
                if ($this->getUser()->getId() === $discuss_name_existing->getCreator()) {
                    //  IF USERS NUMBER < 9 : AJOUT DES MEMBRES + RETURN T0008
                    $count = \count($request_members);
                    $membersAlreadyIn = $discuss_name_existing->getUsers();
                    $finalCount = $count + \count($membersAlreadyIn);
                    if ($finalCount <= 9) {
                        //AJOUTER LE/LES MEMBRES DANS LA BDD
                        $manager = $this->getDoctrine()->getManager();
                        $usersNameArray = [];
                        $user = new User();
                        foreach ($request_members as $members) {
                            $user = $docRepoUser->find($members);
                            $usersNameArray = $user->getUsername();
                            $discuss_name_existing->addUser($user);
                            $user->addGroup($discuss_name_existing);
                        }
                        $this->getUser()->addGroup($discuss_name_existing);
                        $manager->persist($user);
                        $manager->flush();
                        $controller_name = 'discussion';
                        $code = 'T0008';
                        $description = 'Membre(s) ajouté(s) avec succès';
                        $payload = array(
                            'members' => array(
                                $usersNameArray
                            )
                        );
                    } else {
                        //  IF USERS NUMBER + createur > 9 : RETURN E0005 too much people
                        $controller_name = 'error';
                        $code = 'E0005';
                        $description = 'Trop de members tuent les membres';
                        $payload = '';
                    }
                } else // IF NOT : RETURN E006 not discussion creator
                {
                    $controller_name = 'error';
                    $code = 'E0006';
                    $description = 'Vous n\'avez pas le droit d\'effectuer cette manipulation pour cette discussion';
                }
            } else {
                $controller_name = 'error';
                $code = 'E000?';
                $description = 'La discussion n\'existe pas ? Comment etes vous arrivez ici vous !?';
                $payload = '';
            }
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
        $resp_jwt_json = $this->json(array(
            'jwt'=> JWT::encode($resp_content_json,getenv('APP_SECRET'))
        ));

        return $resp_jwt_json;
    }

    /**
     * @Route("/restapi/discussions/leave", name="discussions_leave")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_leave(): Response
    {
        $request_force = (boolean) null;
        $request_discussionId = (string) null;
        $controller_name = (string) null;
        $code = (string) null;
        $description = (string) null;
        $payload = (string) null;
        $request_token = null;

        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key === 'token') {
                $request_token = $value;
            } else if ($key === 'discussionId') {
                $request_discussionId = $value;
            } else if ($key === 'force') {
                $request_force = $value;
            }
        }

        if($request_token !== null && $request_token === $this->getUser()->getApiToken()) {

            if ($request_discussionId !== null) {
                $discuss_name_existing = $this->getDoctrine()
                    ->getRepository(Group::class)
                    ->findOneBy(['discussionName' => $request_discussionId]);

               if($discuss_name_existing){
                   $group_message_existing = $this->getDoctrine()
                       ->getRepository(GroupMessage::class)
                       ->findBy(['group_' =>$discuss_name_existing->getId()]);
                   }
            }

            //CONDITION :
            //  IF SESSIONS TOKEN existe

            //  IF USER IS CREATEUR DISCUSSION
            if ($this->getUser()->getId() === $discuss_name_existing->getCreator()) {
                if ($request_force === true) {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->remove($discuss_name_existing);
                    $em->flush();

                    //      IF force = true : User Leave + Conv delete + message delete : RETURN T0009
                    $controller_name = "discussion";
                    $code = "T0009";
                    $description = "La discussion a été supprimée, ainsi que son historique";
                } else {
                    //      IF NOT : RETURN E0007
                    $controller_name = "error";
                    $code = "E0007";
                    $description = "Pour quitter une conversation dont vous êtes créateur, il faut forcer sa suppression";
                }
            }
            if ($this->getUser()->getGroups()->contains($discuss_name_existing) && $this->getUser()->getId() !== $discuss_name_existing->getCreator()) {
                //  IF USER IS MEMBER OF DISCUSS
                $this->getUser()->removeGroup($discuss_name_existing);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($this->getUser());
                $manager->flush();
                $controller_name = "discussion";
                $code = "T0010";
                $description = "Vous avez quitté la conversation";
            }
            elseif ($this->getUser()->getGroups()->contains($discuss_name_existing) === false) {
                $controller_name = "error";
                $code = "E0008";
                $description = "Vous ne pouvez quitter cette conversation car vous n'en faites par partie ou qu'elle n'existe pas";
            }

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
        $resp_jwt_json = $this->json(array(
            'jwt'=> JWT::encode($resp_content_json,getenv('APP_SECRET'))
        ));                                                         // Creation du JSON contenant jwt: token_jwt
        return $resp_jwt_json;                                     //Envoi du token jwt
    }

    /**
     * @Route("/restapi/discussions/list", name="discussions_list")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function discussions_list(): Response
    {
        $request_force = (boolean) null;
        $request_discussionId = (string) null;
        $controller_name = (string) null;
        $code = (string) null;
        $description = (string) null;
        $infoGroupUser = [];
        //On recupere la requete utilisateur
        $request_str = $this->container->get('request_stack')->getCurrentRequest()->getContent(); //STRING
        $request_json = json_decode($request_str, true); //object JSON
        foreach ($request_json as $key => $value){
            if ($key === 'token') {
                $request_token = $value;
            } else if ($key === 'discussionId') {
                $request_discussionId = $value;
            } else if ($key === 'force') {
                $request_force = $value;
            }
        }

        $controller_name = "discussion";
        $code = "T0011";
        $description = "Liste des discussions auxquelles vous prenez part";

        $payload = '';

        if($request_token !== null && $request_token === $this->getUser()->getApiToken()) {

            //Récupérer le user en cours
            $currentUser = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($this->getUser()->getId());
            if ($currentUser) {
                $groups = $currentUser->getGroups();
                foreach ($groups as $group) {
                    $userGroups[] = $group;
                }
                foreach ($userGroups as $userGroup) {
                    if ($this->getUser()->getId() === $userGroup->getCreator()) {
                        $infoGroupUser[] = array('creator', $userGroup->id, $userGroup->discussionName);
                    } else {
                        $infoGroupUser[] = array('member', $userGroup->id, $userGroup->discussionName);
                    }
                }
            }

            $payload = $infoGroupUser;
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
        $resp_jwt_json = $this->json(array(
            'jwt'=> JWT::encode($resp_content_json,getenv('APP_SECRET'))
        ));                                                         // Creation du JSON contenant jwt: token_jwt
        return $resp_jwt_json;                                     //Envoi du token jwt
    }

}