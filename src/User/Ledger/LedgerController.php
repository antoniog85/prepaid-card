<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger;

use PrepaidCard\BaseController;
use PrepaidCard\HttpStatusCodes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LedgerController extends BaseController
{
    /** @var LedgerService */
    private $service;

    public function __construct(LedgerService $service)
    {
        $this->service = $service;
    }

    public function statement(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $ledgers = $this->service->showStatement($args['id']);
        $result = [];
        foreach ($ledgers as $ledger) {
            $result[] = $ledger->toArray();
        }

        return $this->respond($response, json_encode($result), HttpStatusCodes::HTTP_OK);
    }
}
