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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
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
     * @SWG\Parameter(
     *   name="id",
     *   description="Id of the user",
     *   in="path",
     *   required=true,
     *   type="integer"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Forbidden"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found"
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
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
     * @SWG\Parameter(
     *   name="page",
     *   description="The page number to show",
     *   in="query",
     *   type="integer"
     * )
     * @SWG\Parameter(
     *   name="limit",
     *   description="The number of users per page",
     *   in="query",
     *   type="integer"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *      @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Forbidden"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found"
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
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
     * @SWG\Parameter(
     *   name="User",
     *   description="Fields to provide to create an user",
     *   in="body",
     *   required=true,
     *   type="string",
     *   @SWG\Schema(
     *     type="object",
     *     title="User field",
     *     @SWG\Property(property="first_name", type="string"),
     *     @SWG\Property(property="last_name", type="string"),
     *     @SWG\Property(property="email", type="string"),
     *     @SWG\Property(property="address", type="string"),
     *     @SWG\Property(property="zipcode", type="string"),
     *     @SWG\Property(property="city", type="string")
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Created",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Forbidden"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Internal Server Error"
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
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
     * @SWG\Parameter(
     *   name="id",
     *   description="Id of the user to delete",
     *   in="path",
     *   required=true,
     *   type="integer"
     * )
     * @SWG\Response(
     *     response=204,
     *     description="No Content"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Forbidden"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found"
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
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
