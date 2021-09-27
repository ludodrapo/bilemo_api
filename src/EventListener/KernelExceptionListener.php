<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * class KernelExceptionListener
 * @package App\EventListener
 */
class KernelExceptionListener
{
    /**
     * @param ExceptionEvent $event
     * @return JsonResponse
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception->getCode()) {
            //If exception is thrown manually from the controller
            $code = $exception->getCode();
        } else {
            //If the exception is thrown by the system
            $code = $exception->getStatusCode();
        }

        $message = [
            'code' => $code,
            'message' => $exception->getMessage()
        ];

        $response = new JsonResponse($message);
        
        $response->setStatusCode($code);

        $event->setResponse($response);
    }
}
