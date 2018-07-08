<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger;

use PrepaidCard\MoneyInterface;
use PrepaidCard\Transaction\Transaction;

interface LedgerRepositoryInterface
{
    const ID             = 'ledger-id';
    const TRANSACTION_ID = 'transaction-id';
    const DATE           = 'date';
    const CREDIT         = 'credit';
    const DEBIT          = 'debit';
    const BLOCKED        = 'blocked';
    const DESCRIPTION    = 'description';

    public function get(string $ledgerId): Ledger;

    /**
     * @param string $cardId
     *
     * @return Ledger[]
     */
    public function getByCard(string $cardId): array;

    /**
     * @param string $transactionId
     *
     * @return Ledger[]
     */
    public function getByTransaction(string $transactionId): array;

    public function getCapturedAmount(string $transactionId): MoneyInterface;

    public function transactionHasBeenRefunded(Transaction $transaction): bool;

    public function create(Ledger $ledger): Ledger;
}
