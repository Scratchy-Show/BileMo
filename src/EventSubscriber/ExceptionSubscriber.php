<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        // Récupère l'erreur
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Page not found'
            ];

            // Renvoie une réponse au format JSON
            $event->setResponse(new JsonResponse($data));
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Access to this resource is not allowed'
            ];

            // Renvoie une réponse au format JSON
            $event->setResponse(new JsonResponse($data));
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Method Not Allowed'
            ];

            // Renvoie une réponse au format JSON
            $event->setResponse(new JsonResponse($data));
        } elseif ($exception instanceof BadRequestHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];

            // Renvoie une réponse au format JSON
            $event->setResponse(new JsonResponse($data));
        } else {
            $data = [
                'status' => 500,
                'message' => $exception->getMessage()
            ];

            // Renvoie une réponse au format JSON
            $event->setResponse(new JsonResponse($data));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
