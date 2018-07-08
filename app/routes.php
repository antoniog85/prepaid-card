<?php

use PrepaidCard\Card\CardController;
use PrepaidCard\Transaction\TransactionController;
use PrepaidCard\User\Ledger\LedgerController;
use Slim\App;

$app->group(
    '/card',
    function (App $app) {
        $app->get('/{id}/statement', LedgerController::class . ':statement');
        $app->get('/{id}/transactions', TransactionController::class . ':getByCard');
        $app->post('/{id}/load', CardController::class . ':load');
        $app->get('/{id}', CardController::class . ':get');
        $app->post('/{id}/purchase', CardController::class . ':purchase');
        $app->post('', CardController::class . ':create');
    }
);

$app->group(
    '/transaction',
    function (App $app) {
        $app->put('/{id}/capture', TransactionController::class . ':capture');
        $app->put('/{id}/refund', TransactionController::class . ':refund');
    }
);
