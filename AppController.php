<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use AppBundle\Entity\Connection;
use AppBundle\Entity\FavoriteUser;
use AppBundle\Entity\Group;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Post;
use AppBundle\Entity\PostComment;
use AppBundle\Entity\User;
use AppBundle\Form\Type\CommentType;
use AppBundle\Form\Type\PostType;
use AppBundle\Form\Type\UpdateUserType;
use AppBundle\Form\Type\UserType;
use AppBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route as Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Class AppController
 */
class AppController extends Controller
{
    /**
     * @Route("/home", name="ping_landing_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@App/homepage.html.twig', [
            "msg" => "Hello there, my name is Rick!"
        ]);
    }

    /**
     * @Route("/signup", name="display_signup_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupAction()
    {
        return $this->render('@App/signup.html.twig', [
            "msg" => "Hello there, my name is Rick!"
        ]);
    }

    /**
     * @Route("/signin", name="display_signin_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signInAction()
    {
        return $this->render('@App/login.html.twig', [
            "msg" => "Hello there, my name is Rick!"
        ]);
    }

    /**
     * @Route("/register", name="register_new_user", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $user = new User();
        $em = $this->get('doctrine.orm.entity_manager');

        $form = $this->createForm(UserType::class, $user);
        $form->submit($requestData);

        if ($form->isValid()) {
            $factory = new UserPasswordEncoder($this->get('security.encoder_factory'));
            $cardService = $this->get('ping_card.service');
            $userService = $this->get('ping_user.service');

            $userService->createDefaultSettings($user->getUuid());
            $user = $cardService->createDefaultCards($user);

            $encPassword = $factory->encodePassword($user, $requestData['password']);
            $user->setPassword($encPassword);

            $em->persist($user);

            try {
                $em->flush();
            } catch (\Exception $exception) {
                return new JsonResponse([
                    "msg" => $exception->getMessage()
                ], 200);
            }
            return $this->render('@App/profile.html.twig', [
                "msg" => "You now have a Ping account!"
            ]);
        }

        return $this->render('@App/profile.html.twig', [
            "msg" => $form->getErrors(true)
        ]);
    }

    /**
     * @Route("/login", name="user_login")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $em = $this->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            "email" => $request->get('username')
        ]);

        if ($user) {
            $factory = $this->get('security.encoder_factory');

            $encoder = $factory->getEncoder($user);
            $salt = $user->getSalt();

            if (!$encoder->isPasswordValid($user->getPassword(), $request->get('password'), $salt)) {
                return new JsonResponse([
                    "msg" => "Invalid username or password."
                ], 400);
            }

            $token = new UsernamePasswordToken(
                $user,
                null,
                'main',
                $user->getRoles()
            );

            $authenticationManager = $this->get('security.authentication.manager');
            $token = $authenticationManager->authenticate($token);

            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $this->get('session')->save();

            if ($token->isAuthenticated()) {
                return new JsonResponse([
                    "msg" => "You have logged in!",
                    "userId" => $user->getUuid(),
                ], 200);
            } else {
                return new JsonResponse([
                    "msg" => $token->getCredentials()
                ], 400);
            }

        }

        return new JsonResponse([
            "msg" => "Invalid username or password."
        ], 400);
    }

    /**
     * @Route("/profile/{userId}", name="user_profile", options={"expose"=true})
     *
     * @param Request $request
     * @param string $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(Request $request, $userId)
    {
        $requestData = json_decode($request->getContent(), true);
        $em = $this->get('doctrine.orm.entity_manager');
        $serializer = $this->get('jms_serializer');
        $userService = new UserService($em);

        // TODO: Make sure to remove sensitive data when serializing

        /* @var User $userRaw */
        $userRaw = $em->getRepository(User::class)->findOneBy([
            "uuid" => $userId
        ]);
        $demoUser = $userService->getDemoUserRaw();

        $userClean = $em->getRepository(User::class)->findUserByUuid($userId);
        $connected = $em->getRepository(Connection::class)
            ->connectionExists($demoUser, $userRaw);
//
//        // TODO: Remove after mobile development
//        $connected = $em->getRepository(Connection::class)
//            ->connectionExists($demoUser, $user);

//        $notification = new ProfileViewNotification($userId, User::DEMO_USER);
//        $em->persist($notification);
//        $em->flush();

        if ($requestData['native']) {
            return new JsonResponse([
                "user" => json_encode($userClean),
                "connected" => !$connected ? false : true,
                "ownProfile" => $demoUser === $userRaw
            ], 200);
        }

        return $this->render('@App/profile.html.twig', [
            "user" => json_encode($userClean),
            "connected" => !$connected ? false : true,
            "ownProfile" => $demoUser === $userRaw
        ]);
    }

    /**
     * @Route("/save_profile", name="user_save_profile", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveProfile(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $em = $this->get('doctrine.orm.entity_manager');

        $user = $this->getUser();
        $form = $this->createForm(UpdateUserType::class, $user);
        $form->submit($requestData['user']);

        if ($form->isValid()) {
            $em->persist($user);

            try {
                $em->flush();

                return new JsonResponse([
                    "msg" => "Profile Updated!"
                ], 200);
            } catch (\Exception $exception) {
                return new JsonResponse([
                    "msg" => $exception->getMessage()
                ], 400);
            }
        }

        return new JsonResponse([
            "msg" => $form->getExtraData()
        ], 400);
    }

    /**
     * @Route("/user/{userId}/cards", name="get_user_cards", options={"expose"=true})
     *
     * @param Request $request
     * @param string $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadUserCardsAction(Request $request, $userId)
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $userService = new UserService($em);
        $serializer = $this->get('jms_serializer');

        $user = $userService->getDemoUser();
        $cards = $em->getRepository(Card::class)->findCardsByUserId($user['id']);

        if ($cards) {
            return new JsonResponse([
                "cards" => $serializer->serialize($cards, 'json')
            ], 200);
        }
        return new JsonResponse([
            "msg" => "Could not load user cards"
        ], 400);
    }

    /**
     * @Route("/groups", name="user_groups", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function groupsAction(Request $request)
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $serializer = $this->get('jms_serializer');
        $userService = new UserService($em);
        $user = $userService->getDemoUser();
        $groups = $em->getRepository(Group::class)->getAllUserGroups(User::DEMO_USER);

        return $this->render('@App/groups.html.twig', [
            "user" => json_encode($user),
            "groups" => $serializer->serialize($groups, 'json')
        ]);
    }

    /**
     * @Route("/load_user_groups", name="load_user_groups", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadUserGroupsAction(Request $request)
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $serializer = $this->get('jms_serializer');
        $groups = $em->getRepository(Group::class)->getAllUserGroups(User::DEMO_USER);

        return new JsonResponse([
            "groups" => $serializer->serialize($groups, 'json')
        ], 200);
    }

    /**
     * @Route("/load_group/{groupId}", name="load_group", options={"expose"=true})
     *
     * @param string $groupId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadGroupAction($groupId)
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $serializer = $this->get('jms_serializer');
        $group = $em->getRepository(Group::class)->findOneBy([
            "uuid" => $groupId
        ]);

        return new JsonResponse([
            "group" => $serializer->serialize($group, 'json')
        ], 200);
    }

    /**
     * @Route("/create_group", name="create_group", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createGroupAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $em = $this->get("doctrine.orm.entity_manager");
        $userService = new UserService($em);
        $owner = $userService->getDemoUser()->getUuid();

        $group = new Group();
        $name = trim(strip_tags($requestData["name"]));
        $group->setName($name);
        $group->setOwner($owner);

        $em->persist($group);
        try {
            $em->flush();
            return new JsonResponse([
                "name" => $group->getName(),
                "uuid" => $group->getUuid()
            ], 200);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @Route("/load_group_members", name="load_group_members", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadGroupMembersAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $em = $this->get("doctrine.orm.entity_manager");

        $groupId = $em->getRepository(Group::class)->getGroupId($requestData['uuid']);
        $members = $em->getRepository(User::class)->findGroupMembers($groupId);

        return new JsonResponse([
            "members" => $members
        ], 200);
    }

    /**
     * @Route("/add_to_group/{userId}/{groupId}", name="add_to_group", options={"expose"=true})
     *
     * @param string $userId
     * @param string $groupId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addToGroupAction($userId, $groupId)
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $serializer = $this->get("jms_serializer");

        /** @var User $user * */
        $user = $em->getRepository(User::class)->findOneBy([
            "uuid" => $userId
        ]);
        /** @var Group $group */
        $group = $em->getRepository(Group::class)->findOneBy([
            "uuid" => $groupId
        ]);

//        // TODO: Remove after Mobile Testing
//        return new JsonResponse([
//            "invited" => true
//        ], 200);
        ////////////////////////////////////

        $user->addGroup($group);
        $group->addMember($user);

        $em->persist($user);
        $em->persist($group);

        try {
            $em->flush();
            return new JsonResponse([
                "invited" => true
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResponse([
                "invited" => false
            ], 200);
        }
    }

    /**
     * @Route("/events", name="user_events", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function eventsAction(Request $request)
    {
        return $this->render('@App/events.html.twig');
    }

    /**
     * @Route("/notifications", name="user_notifications", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notificationsAction(Request $request)
    {
        return $this->render('@App/notifications.html.twig');
    }

    /**
     * @Route("/load_notifications", name="load_user_notifications", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadNotificationsAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $notifications = $this->get('doctrine.orm.entity_manager')->getRepository(Notification::class)
            ->getAllNotifications(User::DEMO_USER);

        if ($notifications) {
            return new JsonResponse([
                "notifications" => $serializer->serialize($notifications, 'json')
            ], 200);
        }

        return new JsonResponse([
            "msg" => "Failed to retrieve notifications"
        ], 400);
    }

    /**
     * @Route("/settings", name="user_settings", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction()
    {
//        $userService = $this->get('ping_user.service');
//
//        $user = $this->get('doctrine.orm.entity_manager')->getRepository(User::class)->findOneBy([
//            "uuid" => User::DEMO_USER
//        ]);
//
//        $userService->createDefaultSettings($user->getUuid());

        return $this->render('@App/settings.html.twig');
    }

    /**
     * @Route("/connect/{requestedId}", name="user_connect", options={"expose"=true})
     *
     * @param string $requestedId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectAction($requestedId)
    {
        $userService = $this->get('ping_user.service');
        $user = $userService->getDemoUser();

        $connection = $this->get('ping_card.service')->sendConnectionRequest($user, $requestedId);

        if ($connection) {
            return new JsonResponse([
                "msg" => "Your connection request has been sent."
            ], 200);
        }

        return new JsonResponse([
            "msg" => "Failed to send connection to user."
        ], 400);
    }

    /**
     * @Route("/disconnect/{requestedId}", name="user_disconnect", options={"expose"=true})
     *
     * @param string $requestedId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function disconnectAction($requestedId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $userService = new UserService($em);

        /** @var User $user */
        $user = $userService->getDemoUser();
        $disconnection = $this->get('ping_card.service')->sendDisconnectionRequest($user, $requestedId);

        if ($disconnection) {
            return new JsonResponse([
                "msg" => "You are no longer connected to this user."
            ], 200);
        }

        return new JsonResponse([
            "msg" => "Failed to disconnect from user."
        ], 400);
    }

    /**
     * @Route("/connections", name="user_connections", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectionsAction(Request $request)
    {
        return $this->render('@App/connections.html.twig', [
        ]);
    }

    /**
     * @Route("/load_connections", name="load_connections", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadConnectionsAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $em = $this->get("doctrine.orm.entity_manager");
        $userService = new UserService($em);

        /** @var User $user */
        $user = $userService->getDemoUserRaw();

        $filterBy = $requestData['filterBy'];
        $pagination = $requestData['pagination'];

        if ($filterBy && $user && $pagination) {
            $connections = $this->get('doctrine.orm.entity_manager')->getRepository(Connection::class)
                ->getUserConnections($user, $filterBy, $pagination);

            return new JsonResponse([
                "totalConnections" => intval($connections['totalConnections']),
                "totalPages" => intval($connections['totalPages']),
                "connections" => $connections['connectedTo']
            ], 200);
        }

        return new JsonResponse([
            "msg" => "Invalid Request."
        ], 400);
    }

    /**
     * @Route("/load_users", name="load_users_by_filters", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadUsersAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $em = $this->get("doctrine.orm.entity_manager");
        $userService = new UserService($em);

        /** @var User $user */
        $user = $userService->getDemoUserRaw();

        $filterBy = $requestData['filterBy'];
        $pagination = $requestData['pagination'];

        if ($filterBy && $user && $pagination) {
            $users = $this->get('doctrine.orm.entity_manager')->getRepository(User::class)
                ->getUsersByFilters($user, $filterBy, $pagination);

            return new JsonResponse([
                "totalUsers" => intval($users['totalUsers']),
                "totalPages" => intval($users['totalPages']),
                "users" => $users['users']
            ], 200);
        }

        return new JsonResponse([
            "msg" => "Invalid Request."
        ], 400);
    }

    /**
     * @Route("/add_favorite_user/{userId}", name="add_favorite_user", options={"expose"=true})
     *
     * @param string $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addFavoriteUserAction($userId)
    {
        $userService = $this->get('ping_user.service');
        $em = $this->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $this->get('doctrine.orm.entity_manager')->getRepository(User::class)->findOneBy([
            "uuid" => $userId
        ]);

        if (!$em->getRepository(FavoriteUser::class)->userAlreadyFavorited($userService->getDemoUser(), $user)) {

            $result = $userService->addFavoriteUser($userService->getDemoUser(), $user);

            if ($result) {
                return new JsonResponse([
                    "msg" => "Added User to Favorites."
                ], 200);
            }

            return new JsonResponse([
                "msg" => "Failed Adding User to Favorites."
            ], 400);
        }

        return new JsonResponse([
            "msg" => "User Already Favorited."
        ], 400);
    }

    /**
     * @Route("/remove_favorite_user/{userId}", name="remove_favorite_user", options={"expose"=true})
     *
     * @param string $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeFavoriteUserAction($userId)
    {
        $userService = $this->get('ping_user.service');
        $em = $this->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $this->get('doctrine.orm.entity_manager')->getRepository(User::class)->findOneBy([
            "uuid" => $userId
        ]);

        if ($em->getRepository(FavoriteUser::class)->userAlreadyFavorited($userService->getDemoUser(), $user)) {

            $result = $userService->removeFavoriteUser($userService->getDemoUser(), $user);

            if ($result) {
                return new JsonResponse([
                    "msg" => "Removed User from Favorites."
                ], 200);
            }

            return new JsonResponse([
                "msg" => "Failed Removing Favorite User."
            ], 400);
        }

        return new JsonResponse([
            "msg" => "Invalid Request."
        ], 400);
    }

    /**
     * @Route("/load_group_posts/{groupId}", name="load_group_posts", options={"expose"=true})
     *
     * @param string $groupId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadGroupPostsAction($groupId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->get('doctrine.orm.entity_manager');

        $posts = $em->getRepository(Post::class)->findPostsByGroupId($groupId);

        if ($posts) {
            return new JsonResponse([
                "posts" => $serializer->serialize($posts, 'json'),
            ], 200);
        }

        return new JsonResponse([
            "msg" => "Failed to get posts."
        ], 400);
    }

    /**
     * @Route("/add_new_group_post/", name="add_new_group_post", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addNewGroupPostAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);

        $em = $this->get('doctrine.orm.entity_manager');
        $serializer = $this->get('jms_serializer');
//        $postService = new PostService();

        $post = new Post($this->getUser()->getUuid());

        $form = $this->createForm(PostType::class, $post);
        $form->submit($requestData['post']);

        if ($form->isValid()) {
            $em->persist($post);

            try {
                $em->flush();
                $newPost = $em->getRepository(Post::class)->findPostByUuid($post->getUuid(), $post->getOwner());

                return new JsonResponse([
                    "msg" => "Post Successful",
                    "newPost" => $serializer->serialize($newPost, 'json')
                ], 200);
            } catch (\Exception $exception) {
                return new JsonResponse([
                    "error" => $exception->getTrace()
                ], 400);
            }
        } else {
            return new JsonResponse([
                "error" => "Post Failed"
            ], 400);
        }
    }

    /**
     * @Route("/upload_post_attachment", name="upload_post_attachment", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadPostAttachmentAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->get('doctrine.orm.entity_manager');

        if ($request->request->get('postId')) {
            /** @var Post $post */
            $post = $em->getRepository(Post::class)->findPostByUuid($request->request->get('postId'));
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('attachment');
        $fullPath = $this->getParameter('user_upload_path') . $this->getUser()->getUuid() . '/';

        if (!is_dir($fullPath)) {
            $dir = mkdir($fullPath, 0777, true);
            if (!$dir) {
                return new JsonResponse([
                    "msg" => "Failed to Create Directory"
                ], 400);
            }
        }
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();

        try {
            $moved = $file->move($fullPath, $filename);
        } catch (FileException $exception) {
            return new JsonResponse([
                "msg" => $exception->getMessage()
            ], 400);
        }

        if ($file->getClientSize() >= 50000000 || !in_array($file->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
            return new JsonResponse([
                "msg" => "Invalid File Uploaded"
            ], 400);
        }

        return new JsonResponse([
            "mediaPath" => $fullPath,
            "mediaUrl" => $fullPath . $filename,
            "mediaFilename" => $filename,
            "mediaType" => $file->getClientOriginalExtension()
        ], 200);
    }

    /**
     * @Route("/add_new_post_comment/{postId}", name="add_new_post_comment", options={"expose"=true})
     *
     * @param Request $request
     * @param string $postId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addNewPostCommentAction(Request $request, $postId)
    {
        $requestData = json_decode($request->getContent(), true);

        $em = $this->get('doctrine.orm.entity_manager');
        $serializer = $this->get('jms_serializer');

        $comment = new PostComment($postId);

        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($requestData['comment']);

        $comment->setOwner($this->getUser()->getUuid());

        if ($form->isValid()) {
            $em->persist($comment);
            try {
                $em->flush();
                return new JsonResponse([
                    "msg" => "Comment Posted",
                    "newComment" => $serializer->serialize($comment, 'json'),
                    "postId" => $postId
                ], 200);
            } catch (\Exception $exception) {
                return new JsonResponse([
                    "error" => $exception->getMessage()
                ], 400);
            }
        }

        return new JsonResponse([
            "error" => $form->getErrors()
        ], 400);
    }

    /**
     * @Route("/load_post_comments/{postId}", name="load_post_comments", options={"expose"=true})
     *
     * @param string $postId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadPostCommentsAction($postId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->get('doctrine.orm.entity_manager');

        $comments = $em->getRepository(PostComment::class)->findCommentsByPostId($postId);

        if ($comments) {
            return new JsonResponse([
                "posts" => $serializer->serialize($comments, 'json'),
            ], 200);
        }

        return new JsonResponse([
            "msg" => "Failed to get comments for post."
        ], 400);
    }

//    /**
//     * @Route("/add_like_to_post/{postId}", name="add_like_to_post", options={"expose"=true})
//     *
//     * @param string $postId
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function addLikeToPost() {
//        /** @var User $user */
//        $user = $this->getUser();
//
//        $user->
//    }







}