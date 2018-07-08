<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger;

use PrepaidCard\MoneyInterface;
use PrepaidCard\Transaction\Transaction;
use PrepaidCard\User\Ledger\Operation\LedgerOperationInterface;

class LedgerManager
{
    /** @var LedgerRepositoryInterface */
    private $repository;

    public function __construct(LedgerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $cardId
     *
     * @return Ledger[]
     */
    public function getByCard(string $cardId): array
    {
        return $this->repository->getByCard($cardId);
    }

    public function getCapturedAmount(string $transactionId): MoneyInterface
    {
        return $this->repository->getCapturedAmount($transactionId);
    }

    public function transactionHasBeenRefunded(Transaction $transaction): bool
    {
        return $this->repository->transactionHasBeenRefunded($transaction);
    }

    public function perform(LedgerOperationInterface $operation): Ledger
    {
        $ledger = $operation->instantiate();

        return $this->repository->create($ledger);
    }
}
