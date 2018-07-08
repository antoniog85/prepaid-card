<?php declare(strict_types=1);

namespace Test\Unit\User\Ledger\Operation;

use PrepaidCard\Money;
use PrepaidCard\User\Ledger\Operation\Blocked\AuthorizationRequestLedger;
use PrepaidCard\User\Ledger\Operation\Credit\RefundLedger;
use PrepaidCard\User\Ledger\Operation\Debit\CaptureLedger;
use PrepaidCard\User\Ledger\Operation\LedgerOperationInterface;
use Test\Unit\UnitTestCase;

class OperationTest extends UnitTestCase
{
    public function operationsProvider()
    {
        return [
            [
                AuthorizationRequestLedger::class,
                [
                    'credit'  => '0',
                    'debit'   => '0',
                    'blocked' => '3',
                ],
            ],
            [
                RefundLedger::class,
                [
                    'credit'  => '3',
                    'debit'   => '0',
                    'blocked' => '0',
                ],
            ],
            [
                CaptureLedger::class,
                [
                    'credit'  => '0',
                    'debit'   => '3',
                    'blocked' => '0',
                ],
            ],
        ];
    }

    /**
     * @dataProvider operationsProvider
     */
    public function testItShouldInstantiate(string $className, $expectedResult)
    {
        /** @var LedgerOperationInterface $sut */
        $sut = new $className('id', new Money(3));
        $result = $sut->instantiate();

        self::assertNotNull($result->getTransactionId());
        self::assertNotNull($result->getDate());
        self::assertNotNull($result->getDescription());
        self::assertSame($expectedResult['credit'], $result->getCredit()->getAmount());
        self::assertSame($expectedResult['debit'], $result->getDebit()->getAmount());
        self::assertSame($expectedResult['blocked'], $result->getBlocked()->getAmount());
    }
}
