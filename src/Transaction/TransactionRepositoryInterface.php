<?php declare(strict_types=1);

namespace PrepaidCard\Transaction;

interface TransactionRepositoryInterface
{
    const FIELD_ID          = 'transaction-id';
    const FIELD_DATE        = 'transaction-date';
    const FIELD_CARD_ID     = 'card-id';
    const FIELD_MERCHANT_ID = 'merchant-id';
    const FIELD_AMOUNT      = 'amount';

    public function get(string $transactionId): Transaction;

    /**
     * @param string $cardId
     *
     * @return Transaction[]
     */
    public function getTransactionsByCard(string $cardId): array;

    public function persist(Transaction $transaction): Transaction;
}
