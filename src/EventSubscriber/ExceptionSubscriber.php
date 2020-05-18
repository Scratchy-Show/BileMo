<?php

namespace App\EventSubscriber;

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        // Récupère l'erreur
        $exception = $event->getThrowable();

        // Si c'est une erreur de type NotFoundHttpException
        if ($exception instanceof NotFoundHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'La ressource n\'existe pas'
            ];

            // Renvoie une réponse au format JSON
            $event->setResponse(new JsonResponse($data));
        } elseif ($exception instanceof InvalidArgumentException) {
            $data = [
                'status' => '400',
                'message' => $exception->getMessage()
            ];

            // Renvoie une réponse au format JSON
            $event->setResponse(new JsonResponse($data));
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'L\'accès à cette ressource n\'est pas autorisé'
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
