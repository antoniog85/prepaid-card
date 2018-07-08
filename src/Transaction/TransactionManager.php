<?php declare(strict_types=1);

namespace PrepaidCard\Transaction;

use PrepaidCard\Card\Card;
use PrepaidCard\MoneyInterface;

class TransactionManager
{
    /** @var TransactionRepositoryInterface */
    private $repository;

    public function __construct(TransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function get(string $transactionId): Transaction
    {
        return $this->repository->get($transactionId);
    }

    /**
     * @param string $cardId
     *
     * @return Transaction[]
     */
    public function getTransactionsByCard(string $cardId): array
    {
        return $this->repository->getTransactionsByCard($cardId);
    }

    public function purchase(Card $card, string $merchantId, MoneyInterface $amount): Transaction
    {
        $transaction = $this->repository->persist(new Transaction($card->getCardId(), $merchantId, $amount));

        return $transaction;
    }
}
