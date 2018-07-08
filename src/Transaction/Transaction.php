<?php declare(strict_types=1);

namespace PrepaidCard\Transaction;

use PrepaidCard\MoneyInterface;
use Ramsey\Uuid\Uuid;

class Transaction
{
    /** @var string */
    private $transactionId;

    /** @var \DateTimeInterface */
    private $date;

    /** @var string */
    private $cardId;

    /** @var string */
    private $merchantId;

    /** @var MoneyInterface */
    private $amount;

    public function __construct(
        string $cardId,
        string $merchantId,
        MoneyInterface $amount,
        string $transactionId = null,
        \DateTimeInterface $date = null
    ) {
        $this->transactionId = $transactionId ?? Uuid::uuid4()->toString();
        $this->date = $date ?? new \DateTime();
        $this->cardId = $cardId;
        $this->merchantId = $merchantId;
        $this->amount = $amount;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getCardId(): string
    {
        return $this->cardId;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getAmount(): MoneyInterface
    {
        return $this->amount;
    }

    public function toArray(): array
    {
        return [
            TransactionRepositoryInterface::FIELD_ID          => $this->transactionId,
            TransactionRepositoryInterface::FIELD_DATE        => $this->date->format(DATE_ATOM),
            TransactionRepositoryInterface::FIELD_CARD_ID     => $this->cardId,
            TransactionRepositoryInterface::FIELD_MERCHANT_ID => $this->merchantId,
            TransactionRepositoryInterface::FIELD_AMOUNT      => $this->amount->getAmount(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
