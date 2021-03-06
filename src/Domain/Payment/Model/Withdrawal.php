<?php

namespace Leos\Domain\Payment\Model;

use Leos\Domain\Wallet\Model\Wallet;
use Leos\Domain\Money\ValueObject\Money;
use Leos\Domain\Transaction\Model\AbstractTransaction;
use Leos\Domain\Transaction\ValueObject\TransactionType;
use Leos\Domain\Payment\ValueObject\WithdrawalDetails;

/**
 * Class Withdrawal
 *
 * @package Leos\Domain\Payment\Model
 */
class Withdrawal extends AbstractTransaction
{
    /**
     * Withdrawal constructor.
     * 
     * @param Wallet $wallet
     * @param Money $real
     * @param WithdrawalDetails $details
     */
    public function __construct(Wallet $wallet, Money $real, WithdrawalDetails $details)
    {
        parent::__construct(TransactionType::WITHDRAWAL, $wallet, $real, new Money(0, $real->currency()));
        $this->details = $details;
    }

    /**
     * @return RollbackWithdrawal
     */
    public function rollback(): RollbackWithdrawal
    {
        return new RollbackWithdrawal($this);
    }

    /**
     * @return mixed
     */
    public function details()
    {
        return $this->details;
    }
}
