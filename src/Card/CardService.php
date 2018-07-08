<?php declare(strict_types=1);

namespace PrepaidCard\Card;

use PrepaidCard\MoneyInterface;
use PrepaidCard\Transaction\Transaction;
use PrepaidCard\Transaction\TransactionManager;
use PrepaidCard\User\Ledger\LedgerManager;
use PrepaidCard\User\Ledger\Operation\Blocked\AuthorizationRequestLedger;

class CardService
{
    /** @var CardManager */
    private $cardManager;

    /** @var TransactionManager */
    private $transactionManager;

    /** @var LedgerManager */
    private $ledgerManager;

    public function __construct(
        CardManager $cardManager,
        TransactionManager $transactionManager,
        LedgerManager $ledgerManager
    ) {
        $this->cardManager = $cardManager;
        $this->transactionManager = $transactionManager;
        $this->ledgerManager = $ledgerManager;
    }

    public function get(string $cardId): Card
    {
        return $this->cardManager->get($cardId);
    }

    public function create(string $ownerId): Card
    {
        return $this->cardManager->persist(new Card($ownerId));
    }

    public function load(string $cardId, MoneyInterface $amount): Card
    {
        return $this->cardManager->credit($this->cardManager->get($cardId), $amount);
    }

    public function purchase(string $cardId, string $merchantId, MoneyInterface $amount): Transaction
    {
        $card = $this->get($cardId);

        $this->cardManager->block($card, $amount);
        $transaction = $this->transactionManager->purchase($card, $merchantId, $amount);
        $this->ledgerManager->perform(new AuthorizationRequestLedger($transaction->getTransactionId(), $amount));

        return $transaction;
    }
}
