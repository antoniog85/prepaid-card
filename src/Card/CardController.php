<?php declare(strict_types=1);

namespace PrepaidCard\Card;

use PrepaidCard\BaseController;
use PrepaidCard\HttpStatusCodes;
use PrepaidCard\Money;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CardController extends BaseController
{
    /** @var CardService */
    private $service;

    public function __construct(CardService $service)
    {
        $this->service = $service;
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $card = $this->service->get($args['id']);

        return $this->respond($response, $card->toJson(), HttpStatusCodes::HTTP_OK);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $card = $this->service->create($request->getParsedBody()['owner-id']);

        return $this->respond($response, $card->toJson(), HttpStatusCodes::HTTP_CREATED);
    }

    public function load(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $card = $this->service->load(
            $args['id'],
            new Money($request->getParsedBody()['amount'])
        );

        return $this->respond($response, $card->toJson(), HttpStatusCodes::HTTP_OK);
    }

    public function purchase(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $transaction = $this->service->purchase(
            $args['id'],
            $request->getParsedBody()['merchant-id'],
            new Money($request->getParsedBody()['amount'])
        );

        return $this->respond($response, $transaction->toJson(), HttpStatusCodes::HTTP_OK);
    }
}
