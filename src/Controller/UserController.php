<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{id}", name="show_user", methods={"GET"}, requirements={"id":"\d+"})
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
        if ($user->getCustomer() != $this->getUser())
        {
            // Redirection vers le ExceptionSubscriber
            throw new AccessDeniedHttpException();
        }

        // Sérialisation de $user avec un status 200
        return $this->json($user, 200, [], ['groups' => 'showUser']);
    }

    /**
     * @Route("/", name="list_users", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function listUsersOfCustomer(UserRepository $userRepository)
    {
        // Récupère les client de l'utilisateur(Customer)
        $usersCustomer = $userRepository->findBy(['customer' => $this->getUser()]);

        // Sérialisation de $usersCustomer avec un status 200
       return $this->json($usersCustomer, 200, [], ['groups' => 'listUsersCustomer']);
    }

    /**
     * @Route("/create", name="create_user", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    )
    {
        // Convertis la chaîne en objet User
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        // Le client est celui qui est connecté
        $user->setCustomer($this->getUser());

        // Récupère les éventuelles erreurs
        $errors = $validator->validate($user);
        // Si il y a une erreur
        if(count($errors)) {
            // Sérialisation de $errors avec un status 500
            return $this->json($errors, 500);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $data = [
            'status' => 201,
            'message' => 'L\'utilisateur a bien été ajouté'
        ];
        return $this->json($data, 201);
    }

    /**
     * @Route("/{id}", name="delete_user", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteUser(User $user, EntityManagerInterface $entityManager)
    {
        // Si l'utilisateur n'appartient pas au client connecté
        if ($user->getCustomer() != $this->getUser())
        {
            // Redirection vers le ExceptionSubscriber
            throw new AccessDeniedHttpException();
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'L\'utilisateur a bien été supprimé'
        ];
        return $this->json($data, 200);
    }
}
