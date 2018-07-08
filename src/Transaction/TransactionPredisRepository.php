<?php declare(strict_types=1);

namespace PrepaidCard\Transaction;

use Predis\Client;
use PrepaidCard\Exception\EntityNotFoundException;
use PrepaidCard\Money;
use PrepaidCard\MoneyInterface;

class TransactionPredisRepository implements TransactionRepositoryInterface
{
    const TRANSACTION_IDENTIFIER       = 'transaction:%s';
    const CARD_TRANSACTIONS_IDENTIFIER = 'card:%s:transactions';

    /** @var Client */
    private $storage;

    public function __construct(Client $storage)
    {
        $this->storage = $storage;
    }

    public function get(string $transactionId): Transaction
    {
        $data = $this->storage->hgetall(sprintf(self::TRANSACTION_IDENTIFIER, $transactionId));

        if (empty($data)) {
            throw new EntityNotFoundException(sprintf('Transaction ID "%s" not found', $transactionId));
        }

        return new Transaction(
            $data[TransactionRepositoryInterface::FIELD_CARD_ID],
            $data[TransactionRepositoryInterface::FIELD_MERCHANT_ID],
            new Money($data[TransactionRepositoryInterface::FIELD_AMOUNT]),
            $data[TransactionRepositoryInterface::FIELD_ID],
            \DateTime::createFromFormat(DATE_ATOM, $data[TransactionRepositoryInterface::FIELD_DATE])
        );
    }

    /**
     * @param string $cardId
     *
     * @return Transaction[]
     * @throws EntityNotFoundException
     */
    public function getTransactionsByCard(string $cardId): array
    {
        $transactionIds = $this->storage->smembers(sprintf(self::CARD_TRANSACTIONS_IDENTIFIER, $cardId));

        if (empty($transactionIds)) {
            throw new EntityNotFoundException(sprintf('The card ID "%s" does not have any transaction', $cardId));
        }

        $transactions = [];
        foreach ($transactionIds as $transactionId) {
            $transactions[] = $this->get($transactionId);
        }

        return $transactions;
    }

    public function persist(Transaction $transaction): Transaction
    {
        $this->storage->hmset(
            sprintf(self::TRANSACTION_IDENTIFIER, $transaction->getTransactionId()),
            $transaction->toArray()
        );
        $this->storage->sadd(
            sprintf(self::CARD_TRANSACTIONS_IDENTIFIER, $transaction->getCardId()),
            [$transaction->getTransactionId()]
        );

        return $transaction;
    }
}
