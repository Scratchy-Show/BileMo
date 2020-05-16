<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products{page<\d+>?1}", name="list_products", methods={"GET"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function listProducts(Request $request, ProductRepository $productRepository)
    {
        $limit = $_ENV['LIMIT_PRODUCTS'];

        // Récupère le numéro de page
        $page = $request->query->get('page');

        // Vérifie que $page correspond à un nombre
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument est incorrecte (valeur : ' . $page . ').'
            );
        }

        // Si $page est inférieur à 1
        if ($page < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        // Vérifie que $limit correspond à un nombre
        if (!is_numeric($limit)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument est incorrecte (valeur : ' . $limit . ').'
            );
        }


        // Récupère tous les produits de la table product avec une pagination
        $product = $productRepository->findAllProducts($page, $limit);

        // Sérialisation de $product avec un status 200
        return $this->json($product, 200, []);
    }
}
