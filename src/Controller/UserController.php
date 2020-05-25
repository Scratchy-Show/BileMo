<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Service\Paging;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/")
 */
class UserController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("users/{id}", name="user_detail", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param User $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function showUser(User $user, UserRepository $userRepository)
    {
        // Récupère l'utilisateur
        $user = $userRepository->find($user->getId());

        // Si l'utilisateur n'appartient pas au client connecté
        if ($user->getCustomer() != $this->getUser()) {
            // Redirection vers le ExceptionSubscriber
            throw new AccessDeniedHttpException();
        }

        // Sérialisation de $user
        $json = $this->serializer->serialize($user, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("users", name="users_list", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Paging $paging
     * @return JsonResponse
     */
    public function listUsersOfCustomer(Request $request, Paging $paging)
    {
        $limit = $request->query->get('limit', 5);
        $page = $request->query->get('page', 1);
        $route = $request->attributes->get('_route');

        $paging
            ->setEntityClass(User::class)
            ->setRoute($route);
        $paging
            ->setCurrentPage($page)
            ->setLimit($limit);
        $paging
            ->setCriteria(['customer' => $this->getUser()])
            ->setOrder(['lastName' => 'ASC']);

        $paginated = $paging->getData();

        if ($paginated->getPage() > $paginated->getPages() || $paginated->getPage() < 1) {
            // Redirection vers le ExceptionSubscriber
            throw new NotFoundHttpException();
        }

        $data = $this->serializer->serialize(
            $paginated,
            'json',
            SerializationContext::create()->setGroups(array('Default', 'list'))
        );

        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("users/create", name="user_create", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws Exception
     */
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        // Convertis la chaîne en objet User
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        // La date d'inscription est celle d'aujourd'hui
        $user->setCreatedAt(new DateTime("now"));
        // Le client est celui qui est connecté
        $user->setCustomer($this->getUser());

        // Récupère les éventuelles erreurs
        $errors = $validator->validate($user);
        // Si il y a une erreur
        if (count($errors)) {
            // Sérialisation de $errors avec un status 500
            return $this->json($errors, 500);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $data = [
            'status' => 201,
            'message' => 'User successfully added'
        ];
        return $this->json($data, 201);
    }

    /**
     * @Route("users/{id}", name="user_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteUser(User $user, EntityManagerInterface $entityManager)
    {
        // Si l'utilisateur n'appartient pas au client connecté
        if ($user->getCustomer() != $this->getUser()) {
            // Redirection vers le ExceptionSubscriber
            throw new AccessDeniedHttpException();
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
