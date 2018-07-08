<?php declare(strict_types=1);

namespace PrepaidCard\Transaction;

use PrepaidCard\Card\CardManager;
use PrepaidCard\Exception\OperationForbiddenException;
use PrepaidCard\MoneyInterface;
use PrepaidCard\User\Ledger\LedgerManager;
use PrepaidCard\User\Ledger\Operation\Credit\RefundLedger;
use PrepaidCard\User\Ledger\Operation\Debit\CaptureLedger;

class TransactionService
{
    /** @var TransactionManager */
    private $transactionManager;

    /** @var CardManager */
    private $cardManager;

    /** @var LedgerManager */
    private $ledgerManager;

    public function __construct(
        TransactionManager $transactionManager,
        CardManager $cardManager,
        LedgerManager $ledgerManager
    ) {
        $this->transactionManager = $transactionManager;
        $this->cardManager = $cardManager;
        $this->ledgerManager = $ledgerManager;
    }

    /**
     * @param string $cardId
     *
     * @return Transaction[]
     */
    public function getByCard(string $cardId): array
    {
        return $this->transactionManager->getTransactionsByCard($cardId);
    }

    public function capture(string $transactionId, MoneyInterface $amount): Transaction
    {
        $transaction = $this->transactionManager->get($transactionId);
        $capturedAmount = $this->ledgerManager->getCapturedAmount($transactionId);
        if ($capturedAmount->greaterThanOrEqual($amount)) {
            throw new OperationForbiddenException(
                sprintf(
                    'The transaction ID %s has an amount of %s, of which %s has already been captured,' .
                    'but you are trying to capture %s',
                    $transactionId,
                    $transaction->getAmount()->getAmount(),
                    $capturedAmount->getAmount(),
                    $amount->getAmount()
                )
            );
        }

        $card = $this->cardManager->get($transaction->getCardId());

        $this->cardManager->debit($card, $amount);
        $this->ledgerManager->perform(new CaptureLedger($transactionId, $amount));

        return $transaction;
    }

    public function refund(string $transactionId): Transaction
    {
        $transaction = $this->transactionManager->get($transactionId);
        if ($this->ledgerManager->transactionHasBeenRefunded($transaction)) {
            throw new OperationForbiddenException(
                sprintf('Trying to refund the transaction ID %s which has already been refunded', $transactionId)
            );
        }
        $card = $this->cardManager->get($transaction->getCardId());
        $this->cardManager->refund($card, $transaction->getAmount());
        $this->ledgerManager->perform(new RefundLedger($transactionId, $transaction->getAmount()));

        return $transaction;
    }
}
