<?php declare(strict_types=1);

namespace PrepaidCard\Transaction;

use PrepaidCard\BaseController;
use PrepaidCard\HttpStatusCodes;
use PrepaidCard\Money;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TransactionController extends BaseController
{
    /** @var TransactionService */
    private $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function getByCard(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $transactions = $this->service->getByCard($args['id']);
        $result = [];
        foreach ($transactions as $transaction) {
            $result[] = $transaction->toArray();
        }

        return $this->respond($response, json_encode($result), HttpStatusCodes::HTTP_OK);
    }

    public function capture(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $amount = new Money($request->getParsedBody()['amount']);
        $transaction = $this->service->capture($args['id'], $amount);

        return $this->respond($response, $transaction->toJson(), HttpStatusCodes::HTTP_OK);
    }

    public function refund(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $transaction = $this->service->refund($args['id']);

        return $this->respond($response, $transaction->toJson(), HttpStatusCodes::HTTP_OK);
    }
}
