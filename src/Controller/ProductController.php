<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product", methods={"GET"})
     */
    public function index()
    {
        $product = new Product();
        $product->setBrand("Apple");
        $product->setName("iPhone 11");
        $product->setMemory("64 Go");
        $product->setColor("Noir");
        $product->setPrice(629);
        $product->setDescription("L’appareil photo le plus populaire au monde");

        // Sérialisation de $product avec un status 200
        return $this->json($product, 200, []);
    }
}
