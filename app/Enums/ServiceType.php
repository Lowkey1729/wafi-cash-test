<?php


namespace App\Enums;


use Spatie\Enum\Enum;

/**
 * @method static self WALLET_TRANSFER()
 * @method static self BANK_TRANSFER()
 * @method static self WALLET_TOPUP()
 * @method static self ACCOUNT_REFERENCE()
 * @method static self REVERSAL()
 * @method static self CARD_DEBIT()
 */
class ServiceType extends Enum
{


    protected static function values()
    {
        return [
            'WALLET_TRANSFER' => 'wallet-transfer',
            'BANK_TRANSFER' => 'bank-transfer',
            'WALLET_TOPUP' => 'wallet-topup',
            'ACCOUNT_REFERENCE' => 'account-ref',
            'MANUAL_TRANSFER' => 'manual-transfer',
            'REVERSAL' => 'reversal',
            'CARD_DEBIT' => 'card-debit',
        ];
    }
}
