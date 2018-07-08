<?php declare(strict_types=1);

namespace Test\Unit\Card;

use PHPUnit\Framework\MockObject\MockObject;
use PrepaidCard\Card\CardManager;
use PrepaidCard\Card\CardService;
use PrepaidCard\Money;
use PrepaidCard\Transaction\TransactionManager;
use PrepaidCard\User\Ledger\LedgerManager;
use Test\Unit\UnitTestCase;

class CardServiceTest extends UnitTestCase
{
    /** @var MockObject */
    private $cardManager;

    /** @var MockObject */
    private $transactionManager;

    /** @var MockObject */
    private $ledgerManager;

    /** @var CardService */
    private $sut;

    public function setUp()
    {
        $this->cardManager = $this->createMock(CardManager::class);
        $this->transactionManager = $this->createMock(TransactionManager::class);
        $this->ledgerManager = $this->createMock(LedgerManager::class);
        $this->sut = new CardService($this->cardManager, $this->transactionManager, $this->ledgerManager);
    }

    public function testItShouldGet()
    {
        $this->cardManager
            ->expects(self::once())
            ->method('get');

        $this->sut->get('id');
    }

    public function testItShouldCreate()
    {
        $this->cardManager
            ->expects(self::once())
            ->method('persist');

        $this->sut->create('id');
    }

    public function testItShouldLoad()
    {
        $this->cardManager
            ->expects(self::once())
            ->method('credit');

        $this->sut->load('id', new Money(32));
    }

    public function testItShouldPurchase()
    {
        $this->cardManager
            ->expects(self::once())
            ->method('block');

        $this->transactionManager
            ->expects(self::once())
            ->method('purchase');

        $this->ledgerManager
            ->expects(self::once())
            ->method('perform');

        $this->sut->purchase('id', 'id', new Money(3));
    }
}
