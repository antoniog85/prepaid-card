<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger\Operation\Debit;

use PrepaidCard\MoneyInterface;

class CaptureLedger extends DebitLedger
{
    const DESCRIPTION = 'transaction amount captured';

    public function __construct(string $transactionId, MoneyInterface $amount)
    {
        parent::__construct($transactionId, $amount, self::DESCRIPTION);
    }
}
