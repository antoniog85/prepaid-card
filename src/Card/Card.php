<?php declare(strict_types=1);

namespace PrepaidCard\Card;

use PrepaidCard\Money;
use PrepaidCard\MoneyInterface;
use Ramsey\Uuid\Uuid;

class Card
{
    /** @var string */
    private $cardId;

    /** @var string */
    private $ownerId;

    /** @var MoneyInterface */
    private $balance;

    /** @var MoneyInterface */
    private $blocked;

    public function __construct(
        string $ownerId,
        MoneyInterface $balance = null,
        MoneyInterface $blocked = null,
        string $cardId = null
    ) {
        $this->cardId = $cardId ?? Uuid::uuid4()->toString();
        $this->ownerId = $ownerId;
        $this->balance = $balance ?? new Money(0);
        $this->blocked = $blocked ?? new Money(0);
    }

    public function getCardId(): string
    {
        return $this->cardId;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function credit(MoneyInterface $amount): self
    {
        if ($amount->isZero() || $amount->isNegative()) {
            throw new \InvalidArgumentException('You can\'t credit a with a zero or negative amount');
        }

        $this->balance = $this->balance->add($amount);

        return $this;
    }

    public function debit(MoneyInterface $amount): void
    {
        if ($amount->isZero() || $amount->isNegative()) {
            throw new \InvalidArgumentException('Trying to debit a zero or negative amount');
        }

        if ($this->blocked->subtract($amount)->isNegative()) {
            throw new NotEnoughFundsException(
                sprintf('Not enough blocked funds on the card ID %s to debit %s', $this->cardId, $amount->getAmount())
            );
        }

        $this->blocked = $this->blocked->subtract($amount);
    }

    public function block(MoneyInterface $amount): void
    {
        if ($amount->isZero() || $amount->isNegative()) {
            throw new \InvalidArgumentException('Trying to block a zero or negative amount');
        }

        if ($this->balance->subtract($amount)->isNegative()) {
            throw new NotEnoughFundsException(
                sprintf('Trying to block %s when the balance is %s', $amount->getAmount(), $this->balance->getAmount())
            );
        }

        $this->balance = $this->balance->subtract($amount);
        $this->blocked = $this->blocked->add($amount);
    }

    public function refund(MoneyInterface $amount): void
    {
        $this->balance = $this->balance->add($amount);
    }

    public function toArray(): array
    {
        return [
            CardRepositoryInterface::FIELD_ID      => $this->cardId,
            CardRepositoryInterface::FIELD_OWNER   => $this->ownerId,
            CardRepositoryInterface::FIELD_BALANCE => $this->balance->getAmount(),
            CardRepositoryInterface::FIELF_BLOCKED => $this->blocked->getAmount(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
