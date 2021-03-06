<?php

namespace Tests\Leos\Application\Request\Wallet;
use Leos\Application\UseCase\Transaction\Request\CreateWalletDTO;


/**
 * Class CreateWalletDTOTest
 *
 * @package Tests\Leos\Application\DTO\Wallet
 */
class CreateWalletDTOTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group unit
     */
    public function testGetters()
    {
        $dto = new CreateWalletDTO('EUR');

        self::assertEquals('EUR', $dto->currency()->code());
    }

    /**
     * @group unit
     *
     * @expectedException Leos\Domain\Money\Exception\CurrencyWrongCodeException
     */
    public function testConstructWrongCurrency()
    {
        new CreateWalletDTO('EURAZO');
    }
}
