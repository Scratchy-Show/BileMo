<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

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

        // Sérialisation de $product avec un status 200
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

        // Sérialisation de $product avec un status 200
       return $this->json($usersCustomer, 200, [], ['groups' => 'listUsersCustomer']);
    }
}
