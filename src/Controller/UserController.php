<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/")
 */
class UserController extends AbstractController
{
    /**
     * @Route("users/{id}", name="show_user", methods={"GET"})
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
     * @Route("users", name="list_users", methods={"GET"})
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
     * @Route("users/create", name="create_user", methods={"POST"})
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
     * @Route("users/{id}", name="update_user", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param User $user
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function updateUser(
        Request $request,
        User $user,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager)
    {
        // Si l'utilisateur n'appartient pas au client connecté
        if ($user->getCustomer() != $this->getUser())
        {
            // Redirection vers le ExceptionSubscriber
            throw new AccessDeniedHttpException();
        }

        // Convertis la chaîne en objet User
        $userUpdate = $entityManager->getRepository(User::class)->find($user->getId());

        // Décode les données JSON
        $data = json_decode($request->getContent());

        // Pour chaque données en clé -> valeur
        foreach ($data as $key => $value){
            // Si il y a une clé et que sa valeur n'est pas vide
            if ($key && !empty($value)) {
                // Met la première lettre en Majuscule
                $name = ucfirst($key);
                // Fais correspondre la clé au setter correspondant
                $setter = 'set'.$name;
                // Modifie la valeur du setter
                $userUpdate->$setter($value);
            }
        }

        // Récupère les éventuelles erreurs
        $errors = $validator->validate($user);
        // Si il y a une erreur
        if(count($errors)) {
            // Sérialisation de $errors avec un status 500
            return $this->json($errors, 500);
        }

        $entityManager->flush();

        $data = [
            'status' => 200,
            'message' => 'User has been successfully edited'
        ];
        return $this->json($data, 200);
    }

    /**
     * @Route("users/{id}", name="delete_user", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
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
            'message' => 'User has been successfully deleted'
        ];
        return $this->json($data, 200);
    }
}
