<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger\Operation;

use PrepaidCard\MoneyInterface;

abstract class AbstractLedgerOperation implements LedgerOperationInterface
{
    /** @var string */
    protected $transactionId;

    /** @var MoneyInterface */
    protected $amount;

    /** @var string */
    protected $description;

    public function __construct(string $transactionId, MoneyInterface $amount, string $description)
    {
        $this->transactionId = $transactionId;
        $this->amount = $amount;
        $this->description = $description;
    }
}
