<?php

use PrepaidCard\Card;
use PrepaidCard\Exception\ExceptionHandler;
use PrepaidCard\Transaction;
use PrepaidCard\User\Ledger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;

$container['errorHandler'] = function ($container) {
    return function (ServerRequestInterface $request, ResponseInterface $response, Exception $exception) use ($container
    ) {
        $exceptionHandler = new ExceptionHandler($exception, new NullLogger());

        return $container['response']
            ->withStatus($exceptionHandler->getHttpStatusCode())
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($exceptionHandler->render()));
    };
};

$container['predis'] = function () {
    $config = [
        'schema' => 'tcp',
        'host'   => 'redis',
        'port'   => 6379,
    ];

    return new Predis\Client($config);
};

// repositories

$container['transaction-repository'] = function (ContainerInterface $container) {
    return new Transaction\TransactionPredisRepository($container->get('predis'));
};

// managers

$container['card-manager'] = function (ContainerInterface $container) {
    return new Card\CardManager(new Card\CardPredisRepository($container->get('predis')));
};

$container['transaction-manager'] = function (ContainerInterface $container) {
    return new Transaction\TransactionManager($container->get('transaction-repository'));
};

$container['ledger-manager'] = function (ContainerInterface $container) {
    return new Ledger\LedgerManager(
        new Ledger\LedgerPredisRepository($container->get('predis'), $container->get('transaction-repository'))
    );
};

// services

$container['card-service'] = function (ContainerInterface $container) {
    return new Card\CardService(
        $container->get('card-manager'),
        $container->get('transaction-manager'),
        $container->get('ledger-manager')
    );
};

$container['transaction-service'] = function (ContainerInterface $container) {
    return new Transaction\TransactionService(
        $container->get('transaction-manager'),
        $container->get('card-manager'),
        $container->get('ledger-manager')
    );
};

$container['ledger-service'] = function (ContainerInterface $container) {
    return new Ledger\LedgerService($container->get('ledger-manager'));
};

// controllers

$container[Card\CardController::class] = function (ContainerInterface $container) {
    return new Card\CardController($container->get('card-service'));
};

$container[Transaction\TransactionController::class] = function (ContainerInterface $container) {
    return new Transaction\TransactionController($container->get('transaction-service'));
};

$container[Ledger\LedgerController::class] = function (ContainerInterface $container) {
    return new Ledger\LedgerController($container->get('ledger-service'));
};
