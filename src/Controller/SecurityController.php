<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login_check", name="login", methods={"POST"})
     * @SWG\Parameter(
     *   name="Login",
     *   description="Fields to provide to sign in and get a token",
     *   in="body",
     *   required=true,
     *   type="string",
     *   @SWG\Schema(
     *     type="object",
     *     title="Login field",
     *     @SWG\Property(property="username", type="string"),
     *     @SWG\Property(property="password", type="string")
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *      type="string",
     *      title="Token",
     *      @SWG\Property(property="token", type="string"),
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad request",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized",
     * )
     * @SWG\Tag(name="Login")
     * @return void
     */
    public function login()
    {
    }
}
