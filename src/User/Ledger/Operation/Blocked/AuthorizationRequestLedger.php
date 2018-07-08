<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger\Operation\Blocked;

use PrepaidCard\MoneyInterface;

class AuthorizationRequestLedger extends BlockedLedger
{
    const DESCRIPTION = 'authorization request sent';

    public function __construct(string $transactionId, MoneyInterface $amount)
    {
        parent::__construct($transactionId, $amount, self::DESCRIPTION);
    }
}
