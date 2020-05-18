<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/products")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/{id}", name="show_product", methods={"GET"}, requirements={"id":"\d+"})
     * @param Product $product
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function showProduct(Product $product, ProductRepository $productRepository)
    {
        $product = $productRepository->find($product->getId());

        // Sérialisation de $product avec un status 200
        return $this->json($product, 200, []);
    }

    /**
     * @Route("/{page<\d+>?1}", name="list_products", methods={"GET"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function listProducts(Request $request, ProductRepository $productRepository)
    {
        // Récupère le numéro de page
        $page = $request->query->get('page');

        // Si aucune page d'indiqué - Page 1 par défaut
        if (is_null($page)) {
            $page = 1;
        }

        // Récupère tous les produits de la table product avec une pagination
        $products = $productRepository->findAllProducts($page, $_ENV['LIMIT_PRODUCTS']);

        // Sérialisation de $products avec un status 200
        return $this->json($products, 200, []);
    }
}
