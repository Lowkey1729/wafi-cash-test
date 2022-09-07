<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

class WalletServices
{

    public static function get($var)
    {
        $user = User::query()
            ->where('name', $var)
            ->first();

        if ($user) {
            $wallet = $user->wallet()->isVirtual()->first();
            if (!$wallet) {
                throw new RuntimeException('Cannot find virtual account.', 404);
            }
            return $wallet;
        }
        throw new RuntimeException('Cannot find virtual account.', 404);
    }

    public static function getViaAccountNumber($var)
    {
        $wallet = Wallet::query()->where('account_number', $var)->first();
        if (!$wallet) {
            throw new RuntimeException('Cannot find virtual account.', 404);
        }
        return $wallet;

    }


}
