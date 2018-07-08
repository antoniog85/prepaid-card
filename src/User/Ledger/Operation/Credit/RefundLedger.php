<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger\Operation\Credit;

use PrepaidCard\MoneyInterface;

class RefundLedger extends CreditLedger
{
    const DESCRIPTION = 'transaction refunded';

    public function __construct(string $transactionId, MoneyInterface $amount)
    {
        parent::__construct($transactionId, $amount, self::DESCRIPTION);
    }
}
