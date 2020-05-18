<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="list_users", methods={"GET"})
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
