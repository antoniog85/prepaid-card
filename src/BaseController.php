<?php declare(strict_types=1);

namespace PrepaidCard;

use Psr\Http\Message\ResponseInterface;

abstract class BaseController
{
    public function respond(ResponseInterface $response, $data, int $statusCode): ResponseInterface
    {
        $newResponse = $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus($statusCode);

        $newResponse->getBody()
            ->write($data);

        return $newResponse;
    }
}
