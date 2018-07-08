<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger;

use PrepaidCard\MoneyInterface;
use Ramsey\Uuid\Uuid;

class Ledger
{
    /** @var string */
    private $ledgerId;

    /** @var string */
    private $transactionId;

    /** @var \DateTimeInterface */
    private $date;

    /** @var MoneyInterface */
    private $credit;

    /** @var MoneyInterface */
    private $debit;

    /** @var MoneyInterface */
    private $blocked;

    /** @var string */
    private $description;

    public function __construct(
        string $transactionId,
        MoneyInterface $credit,
        MoneyInterface $debit,
        MoneyInterface $blocked,
        string $description = '',
        string $ledgerId = null,
        \DateTimeInterface $date = null
    ) {
        $this->ledgerId = $ledgerId ?? Uuid::uuid4()->toString();
        $this->transactionId = $transactionId;
        $this->date = $date ?? new \DateTime();
        $this->credit = $credit;
        $this->debit = $debit;
        $this->blocked = $blocked;
        $this->description = $description;
    }

    public function getLedgerId(): string
    {
        return $this->ledgerId;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getCredit(): MoneyInterface
    {
        return $this->credit;
    }

    public function getDebit(): MoneyInterface
    {
        return $this->debit;
    }

    public function getBlocked(): MoneyInterface
    {
        return $this->blocked;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            LedgerRepositoryInterface::ID             => $this->ledgerId,
            LedgerRepositoryInterface::TRANSACTION_ID => $this->transactionId,
            LedgerRepositoryInterface::DATE           => $this->date->format(DATE_ATOM),
            LedgerRepositoryInterface::CREDIT         => $this->credit->getAmount(),
            LedgerRepositoryInterface::DEBIT          => $this->debit->getAmount(),
            LedgerRepositoryInterface::BLOCKED        => $this->blocked->getAmount(),
            LedgerRepositoryInterface::DESCRIPTION    => $this->description,
        ];
    }
}
