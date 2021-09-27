<?php

namespace App\Normalizer;

use Symfony\Component\HttpFoundation\Response;

class NotFoundHttpExceptionNormalizer extends AbstractNormalizer
{
    public function normalize(\Exception $exception)
    {
        $response['code'] = Response::HTTP_NOT_FOUND;

        $response['body'] = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => $exception->getMessage()
        ];

        return $response;
    }
}