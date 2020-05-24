<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Paging;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/products")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/{id}", name="show_product", methods={"GET"})
     * @param Product $product
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function showProduct(Product $product, ProductRepository $productRepository)
    {
        // Récupère le produit
        $product = $productRepository->find($product->getId());

        // Sérialisation de $product avec un status 200
        return $this->json($product, 200, []);
    }

    /**
     * @Route("/", name="list_products", methods={"GET"})
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param Paging $paging
     * @return JsonResponse
     */
    public function listProducts(SerializerInterface $serializer, Request $request, Paging $paging)
    {

        $limit = $request->query->get('limit', 3);
        $page = $request->query->get('page', 1);
        $route = $request->attributes->get('_route');

        $criteria = !empty($request->query->get('name')) ? ['name' => $request->query->get('name')] : [];

        $paging
            ->setEntityClass(Product::class)
            ->setRoute($route);
        $paging
            ->setCurrentPage($page)
            ->setLimit($limit);
        $paging
            ->setCriteria($criteria)
            ->setOrder(['brand' => 'ASC']);

        $paginated = $paging->getData();

        if ($paginated->getPage() > $paginated->getPages() || $paginated->getPage() < 1)
        {
            // Redirection vers le ExceptionSubscriber
            throw new NotFoundHttpException();
        }

        $data = $serializer->serialize($paginated, 'json');

        return new JsonResponse($data, 200, [], true);
    }
}
