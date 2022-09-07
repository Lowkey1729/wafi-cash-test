<?php


namespace App\Helpers;


use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletTransactionService
{

    public static function transfer(int $fromWalletId, int $toWalletId, float $amount, string $reference, $narration, $toNarration = null): WalletTransaction
    {
        $amount = abs($amount);

        return DB::transaction(static function () use ($toNarration, $fromWalletId, $toWalletId, $amount, $reference, $narration) {

            if ( $fromWalletId === $toWalletId) {
                throw new RuntimeException('You cannot perform this operation.', 402);
            }

            //check if wallet to be debited exists
            $fromWallet = Wallet::query()->lockForUpdate()->find($fromWalletId);
            if ( !$fromWallet->exists() ) {
                throw new RuntimeException('Invalid wallet account.', 402);
            }

            //check if wallet to be credited exists
            $toWallet = Wallet::query()->lockForUpdate()->find($toWalletId);
            if ( !$toWallet->exists() ) {
                throw new RuntimeException('Invalid wallet account', 402);
            }

            //check for enough balance
            //also check if wallet is virtual and can be negative balance
            if ( !$fromWallet->is_virtual && !$fromWallet->can_be_negative && $amount > $fromWallet->balance ) {

                throw new RuntimeException('Insufficient balance.', 402);
            }

            $fromWalletPrevBalance = $fromWallet->balance;
            $fromWalletNewBalance = $fromWallet->balance - $amount;
            $fromWallet->balance = $fromWalletNewBalance;
            $fromWallet->save();

            $toWalletPrevBalance = $toWallet->balance;
            $toWalletNewBalance = $toWallet->balance + $amount;

            $toWallet->balance = $toWalletNewBalance;
            $toWallet->save();


            /*
             * "FromWallet", "payer" transaction
             */
            $fromWalletTranx = new WalletTransaction();
            $fromWalletTranx->wallet_id = $fromWallet->id;
            $fromWalletTranx->user_id = $fromWallet->user->id;
            $fromWalletTranx->reference = $reference;
            $fromWalletTranx->amount = $amount;
            $fromWalletTranx->prev_balance = $fromWalletPrevBalance;
            $fromWalletTranx->new_balance = $fromWalletNewBalance;
            $fromWalletTranx->status = 'SUCCESSFUL';
            $fromWalletTranx->type = 'DEBIT';
            $fromWalletTranx->info = $narration;
            $fromWalletTranx->save();


            /**
             * "ToWallet", "receiver"
             */
            $toWalletTranx = new WalletTransaction();
            $toWalletTranx->wallet_id = $toWallet->id;
            $toWalletTranx->user_id = $toWallet->user_id;
            $toWalletTranx->reference = $reference;
            $toWalletTranx->amount = $amount;
            $toWalletTranx->prev_balance = $toWalletPrevBalance;
            $toWalletTranx->new_balance = $toWalletNewBalance;
            $toWalletTranx->status = 'SUCCESSFUL';
            $toWalletTranx->type = 'CREDIT';
            $toWalletTranx->info = $toNarration ?? $narration;
            $toWalletTranx->save();

            return $fromWalletTranx;
        });
    }
}
