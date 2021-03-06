<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Paging;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/")
 */
class ProductController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("products/{id}", name="product_details", methods={"GET"})
     * @SWG\Parameter(
     *   name="id",
     *   description="Id of the product",
     *   in="path",
     *   required=true,
     *   type="integer"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
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
     * @SWG\Tag(name="Products")
     * @Security(name="Bearer")
     * @IsGranted("ROLE_ADMIN")
     * @param Product $product
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function showProduct(Product $product, ProductRepository $productRepository)
    {
        // Récupère le produit
        $product = $productRepository->find($product->getId());

        // Sérialisation de $product
        $json = $this->serializer->serialize($product, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("products", name="products_list", methods={"GET"})
     * @SWG\Parameter(
     *   name="page",
     *   description="The page number to show",
     *   in="query",
     *   type="integer"
     * )
     * @SWG\Parameter(
     *   name="limit",
     *   description="The number of products per page",
     *   in="query",
     *   type="integer"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *      @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
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
     *     response=400,
     *     description="Bad Request"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found"
     * )
     * @SWG\Tag(name="Products")
     * @Security(name="Bearer")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Paging $paging
     * @return JsonResponse
     */
    public function listProducts(Request $request, Paging $paging)
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
}
