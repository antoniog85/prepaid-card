<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger\Operation\Credit;

use PrepaidCard\User\Ledger\Ledger;
use PrepaidCard\Money;
use PrepaidCard\User\Ledger\Operation\AbstractLedgerOperation;

class CreditLedger extends AbstractLedgerOperation
{
    public function instantiate(): Ledger
    {
        return new Ledger(
            $this->transactionId,
            $this->amount,
            new Money(0),
            new Money(0),
            $this->description
        );
    }
}
