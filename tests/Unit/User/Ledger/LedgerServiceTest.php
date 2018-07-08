<?php declare(strict_types=1);

namespace Test\Unit\User\Ledger;

use PHPUnit\Framework\MockObject\MockObject;
use PrepaidCard\User\Ledger\LedgerManager;
use PrepaidCard\User\Ledger\LedgerService;
use Test\Unit\UnitTestCase;

class LedgerServiceTest extends UnitTestCase
{
    /** @var MockObject */
    private $manager;

    /** @var LedgerService */
    private $sut;

    public function setUp()
    {
        $this->manager = $this->createMock(LedgerManager::class);
        $this->sut = new LedgerService($this->manager);
    }

    public function testItShouldShowStatement()
    {
        $this->manager
            ->expects(self::once())
            ->method('getByCard');

        $this->sut->showStatement('id');
    }
}
