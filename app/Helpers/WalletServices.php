<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

class WalletServices
{

    public static function get($var)
    {
        $user = User::query()
            ->with(['wallet'])
            ->where(function (Builder $query) use ($var) {
                $query->where('name', $var)
                    ->orWhereHas("wallet", function (Builder $query) use ($var) {
                        $query->where('account_number', $var);
                    });
            })
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


}
