<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger;

use Predis\Client;
use PrepaidCard\Exception\EntityNotFoundException;
use PrepaidCard\Money;
use PrepaidCard\MoneyInterface;
use PrepaidCard\Transaction\Transaction;
use PrepaidCard\Transaction\TransactionPredisRepository;

class LedgerPredisRepository implements LedgerRepositoryInterface
{
    const LEDGER_IDENTIFIER              = 'ledger:%s';
    const CARD_LEDGERS_IDENTIFIER        = 'card:%s:ledgers';
    const TRANSACTION_LEDGERS_IDENTIFIER = 'transaction:%s:ledgers';

    /** @var Client */
    private $storage;

    /** @var TransactionPredisRepository */
    private $transactionRepository;

    public function __construct(Client $storage, TransactionPredisRepository $transactionRepository)
    {
        $this->storage = $storage;
        $this->transactionRepository = $transactionRepository;
    }

    public function get(string $ledgerId): Ledger
    {
        $data = $this->storage->hgetall(sprintf(self::LEDGER_IDENTIFIER, $ledgerId));

        if (empty($data)) {
            throw new EntityNotFoundException(sprintf('Ledger ID "%s" not found', $ledgerId));
        }

        return new Ledger(
            $data[LedgerRepositoryInterface::TRANSACTION_ID],
            new Money($data[LedgerRepositoryInterface::CREDIT]),
            new Money($data[LedgerRepositoryInterface::DEBIT]),
            new Money($data[LedgerRepositoryInterface::BLOCKED]),
            $data[LedgerRepositoryInterface::DESCRIPTION],
            $data[LedgerRepositoryInterface::ID],
            \DateTime::createFromFormat(DATE_ATOM, $data[LedgerRepositoryInterface::DATE])
        );
    }

    /**
     * @param string $cardId
     *
     * @return Ledger[]
     * @throws EntityNotFoundException
     */
    public function getByCard(string $cardId): array
    {
        $ledgerIds = $this->storage->smembers(sprintf(self::CARD_LEDGERS_IDENTIFIER, $cardId));

        if (empty($ledgerIds)) {
            throw new EntityNotFoundException(sprintf('The card ID "%s" does not have any ledger', $cardId));
        }

        $ledgers = [];
        foreach ($ledgerIds as $ledgerId) {
            $ledgers[] = $this->get($ledgerId);
        }

        return $ledgers;
    }

    /**
     * @param string $transactionId
     *
     * @return Ledger[]
     * @throws EntityNotFoundException
     */
    public function getByTransaction(string $transactionId): array
    {
        $ledgerIds = $this->storage->smembers(sprintf(self::TRANSACTION_LEDGERS_IDENTIFIER, $transactionId));

        if (empty($ledgerIds)) {
            throw new EntityNotFoundException(sprintf('The transaction ID "%s" does not have any ledger', $transactionId));
        }

        $ledgers = [];
        foreach ($ledgerIds as $ledgerId) {
            $ledgers[] = $this->get($ledgerId);
        }

        return $ledgers;
    }

    public function getCapturedAmount(string $transactionId): MoneyInterface
    {
        $transactionLedgers = $this->getByTransaction($transactionId);
        $total = new Money(0);
        foreach ($transactionLedgers as $ledger) {
            $total = $total->add($ledger->getDebit());
        }

        return $total;
    }

    public function transactionHasBeenRefunded(Transaction $transaction): bool
    {
        $transactionLedgers = $this->getByTransaction($transaction->getTransactionId());
        foreach ($transactionLedgers as $ledger) {
            if ($ledger->getCredit()->equals($transaction->getAmount())) {
                return true;
            }
        }

        return false;
    }

    public function create(Ledger $ledger): Ledger
    {
        $transaction = $this->transactionRepository->get($ledger->getTransactionId());

        $this->storage->hmset(sprintf(self::LEDGER_IDENTIFIER, $ledger->getLedgerId()), $ledger->toArray());
        $this->storage->sadd(sprintf(self::CARD_LEDGERS_IDENTIFIER, $transaction->getCardId()), [$ledger->getLedgerId()]);
        $this->storage->sadd(sprintf(self::TRANSACTION_LEDGERS_IDENTIFIER, $transaction->getTransactionId()), [$ledger->getLedgerId()]);

        return $ledger;
    }
}
