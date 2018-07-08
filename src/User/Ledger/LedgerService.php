<?php declare(strict_types=1);

namespace PrepaidCard\User\Ledger;

class LedgerService
{
    /** @var LedgerManager */
    private $ledgerManager;

    public function __construct(LedgerManager $ledgerManager)
    {
        $this->ledgerManager = $ledgerManager;
    }

    /**
     * @param string $cardId
     *
     * @return Ledger[]
     */
    public function showStatement(string $cardId): array
    {
        return $this->ledgerManager->getByCard($cardId);
    }
}
