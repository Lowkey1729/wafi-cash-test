<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiResponseEnum;
use App\Enums\ServiceType;
use App\Helpers\ApiResponse;
use App\Helpers\GeneralHelper;
use App\Helpers\WalletServices;
use App\Helpers\WalletTransactionService;
use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;

class WalletController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->user()->wallet;
        if ($data) {
            return response()->json([
                'status' => ApiResponseEnum::statusSuccess()->value,
                'message' => 'Wallet fetched successfully.',
                'result' => [
                    'data' => $data
                ],
            ]);
        }

        return response()->json([
            'status' => ApiResponseEnum::statusFailed()->value,
            'message' => 'User has no wallet',
            'result' => [
                'data' => null
            ],
        ], 404);
    }


    public function fundWallet(Request $request): JsonResponse
    {

        $validated = $request->validate($this->rules());
        $user = $request->user();
        try {
            $reference = GeneralHelper::generateTransactionReference(ServiceType::WALLET_TOPUP()->value);
            WalletTransactionService::transfer(
                WalletServices::get('wallet-service')->id, $user->walletId(), $validated['amount'], $reference,
                sprintf('Wallet top up of N%s via account transfer (flutter-wave) | %s', $validated['amount'], $reference)
            );
            return response()->json([
                'status' => ApiResponseEnum::statusSuccess(),
                'message' => "Wallet Credited successfully.",
                'result' => [
                    'data' => $user->wallet()->first(),
                ]
            ]);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
//            $message = 'An unexpected error was encountered.';
            return ApiResponse::failed($message, '', '', '500');
        }

    }

    public function walletToWalletTransfer(Request $request): JsonResponse
    {

        $validated = $request->validate($this->walletToWalletRules());
        $user = $request->user();
        try {
            $reference = GeneralHelper::generateTransactionReference(ServiceType::WALLET_TRANSFER()->value);
            $beneficiary = WalletServices::getViaAccountNumber($validated['account_number']);
            WalletTransactionService::transfer(
                $user->walletId(), $beneficiary->id, $validated['amount'], $reference,
                sprintf('Wallet to Wallet Transfer of N%s to  %s | %s', $validated['amount'], $validated['account_number'], $reference)
            );
            return response()->json([
                'status' => ApiResponseEnum::statusSuccess(),
                'message' => sprintf('Wallet transfer to %s executed successfully.', $validated['account_number']),
                'result' => [
                    'data' => [
                        'transfer_from' => $user->wallet()->first(),
                        'transfer_to' => $beneficiary->first()
                    ],
                ]
            ]);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
//            $message = 'An unexpected error was encountered.';
            return ApiResponse::failed($message, '', '',
                $exception->getCode() !== 0 ? $exception->getCode() : 500);
        }

    }

    protected function rules(): array
    {
        return [
            'amount' => ['required']
        ];
    }


    protected function walletToWalletRules(): array
    {
        return [
            'amount' => ['required'],
            'account_number' => ['required']
        ];
    }
}
