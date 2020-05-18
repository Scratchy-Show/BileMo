<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/customer")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="customer")
     */
    public function index()
    {

    }
}
