<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger\Operation\Debit;

use PrepaidCard\User\Ledger\Ledger;
use PrepaidCard\Money;
use PrepaidCard\User\Ledger\Operation\AbstractLedgerOperation;

class DebitLedger extends AbstractLedgerOperation
{
    public function instantiate(): Ledger
    {
        return new Ledger(
            $this->transactionId,
            new Money(0),
            $this->amount,
            new Money(0),
            $this->description
        );
    }
}
