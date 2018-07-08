<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger\Operation;

use PrepaidCard\User\Ledger\Ledger;

interface LedgerOperationInterface
{
    public function instantiate(): Ledger;
}
