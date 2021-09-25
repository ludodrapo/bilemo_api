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
            $code = $exception->getCode();
        } else {
            $code = $exception->getStatusCode();
        }

        $message = [
            'code' => $code,
            'message' => $exception->getMessage()
        ];

        $response = new JsonResponse($message);

        if ($exception instanceof HttpExceptionInterface) {
            if ($exception->getStatusCode() == 404) {
                $notFoundMessage = [
                    'code' => 404,
                    'message' => 'The resource(s) you asked for do(es) not exist (at least not anymore).'
                ];
                $response = new JsonResponse($notFoundMessage);
            }
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode($code);
        }

        $event->setResponse($response);
    }
}
