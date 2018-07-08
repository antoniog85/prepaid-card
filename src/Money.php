<?php declare(strict_types=1);

namespace PrepaidCard;

use Money\Currency;

class Money implements MoneyInterface
{
    /** @var \Money\Money */
    private $money;

    public function __construct($amount, string $currency = MoneyInterface::DEFAULT_CURRENCY)
    {
        $this->money = new \Money\Money($amount, new Currency($currency));
    }

    public function getAmount(): string
    {
        return $this->money->getAmount();
    }

    public function add(MoneyInterface $addend): MoneyInterface
    {
        $addend = new \Money\Money($addend->getAmount(), $this->money->getCurrency());

        $result = $this->money->add($addend);

        return new self($result->getAmount(), $result->getCurrency()->getCode());
    }

    public function subtract(MoneyInterface $subtrahend): MoneyInterface
    {
        $subtrahend = new \Money\Money($subtrahend->getAmount(), $this->money->getCurrency());
        $result = $this->money->subtract($subtrahend);

        return new self($result->getAmount(), $result->getCurrency()->getCode());
    }

    public function isNegative(): bool
    {
        return $this->money->isNegative();
    }

    public function isZero(): bool
    {
        return $this->money->isZero();
    }

    public function equals(MoneyInterface $other): bool
    {
        $other = new \Money\Money($other->getAmount(), $this->money->getCurrency());

        return $this->money->equals($other);
    }

    public function greaterThanOrEqual(MoneyInterface $other): bool
    {
        $other = new \Money\Money($other->getAmount(), $this->money->getCurrency());

        return $this->money->greaterThanOrEqual($other);
    }
}
