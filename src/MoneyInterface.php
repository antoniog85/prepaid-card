<?php declare(strict_types=1);

namespace PrepaidCard;

interface MoneyInterface
{
    const DEFAULT_CURRENCY = 'GBP';

    public function getAmount(): string;

    public function add(self $addend): self;

    public function subtract(self $subtrahend): self;

    public function isNegative(): bool;

    public function isZero(): bool;

    public function equals(MoneyInterface $other): bool;

    public function greaterThanOrEqual(self $other): bool;
}
