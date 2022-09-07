<?php

namespace App\Helpers;


use App\Enums\ServiceType;
use App\Services\AES_Encryption;
use Illuminate\Support\Str;


class GeneralHelper
{

    public static function generateTransactionReference($type): string
    {
        $service_types = self::serviceTypes();
        $leading = 'FLXP';
        $time = substr(time(), -4);
        $str = Str::upper(Str::random(4));
        $service_type = array_key_exists($type, $service_types) ? $service_types[$type] : 'TRNX';

        return sprintf(
            '%s|%s|%s%s',
            $leading,
            $service_type ?? "rand(1111, 9999)",
            $time,
            $str
        );
    }

    protected static function serviceTypes(): array
    {
        return $service_types = [
            ServiceType::BANK_TRANSFER()->value => 'TRSF',
            ServiceType::WALLET_TOPUP()->value => 'WLTP',
            ServiceType::WALLET_TRANSFER()->value => 'WLTR',
            ServiceType::ACCOUNT_REFERENCE()->value => 'AREF',
            ServiceType::REVERSAL()->value => 'RVSL',
            ServiceType::CARD_DEBIT()->value => 'CRDB',
        ];
    }

    public static function formatAmount(float $amount): string|float
    {
        return number_format($amount, 2);
    }

    public static function bankTransferPayload($data): array
    {
        return [
            'bank_code' => $data['bank_code'],
            'account_number' => $data['account_number'],
            'amount' => $data['amount'],
            'redirect_url' => $data['redirect_url'],
            'full_name' => $data['full_name'],
            'reference' => $data['reference']
        ];
    }

    public static function AESKey(): string
    {
        return 'fd4cf79c21642e64f352007442523701cb3bf74c4c37f3e2e432688a834fc7fb';
    }

    public static function AES_IV(): string
    {
        return 'cf4879a288df134f';
    }

    public static function GlobalAESEncrypt($data): string
    {
        $key = self::AESKey();
        $iv = self::AES_IV();
        $aes = new AES_Encryption($key, $iv);
        return $aes->encrypt($data);
    }

    public static function GlobalAESDecrypt($data): string
    {
        $key = self::AESKey();
        $iv = self::AES_IV();
        $aes = new AES_Encryption($key, $iv);
        return $aes->decrypt($data);
    }

    public static function generateUUID($model): string
    {
        $uuid = Str::uuid();
        if ($model->where('uuid', $uuid)->first()) {
            self::generateUUID($model);
        }
        return $uuid;
    }

    protected static function generateSku($model): string
    {
        $sku = Str::sku($model->name, '-');
        if ($model::query()->where('sku', $sku)->first()) {
            self::generateSku($model);
        }
        return $sku;
    }

}
