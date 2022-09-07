<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\{RegisterController, LoginController};
use App\Http\Controllers\Api\{UserController, WalletController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('/register', RegisterController::class)->name('auth.register');
    Route::post('/login', LoginController::class)->name('auth.login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('details', [UserController::class, 'index'])->name('user.details');
    });

    Route::prefix('wallet')->group(function () {
        Route::get('details', [WalletController::class, 'index'])->name('user.wallet.details');
        Route::post('fund', [WalletController::class, 'fundWallet'])->name('user.wallet.fund');
        Route::post('wallet-to-wallet', [WalletController::class, 'walletToWalletTransfer'])->name('user.wallet.wallet-to-wallet');
        Route::get('transactions', [WalletController::class, 'walletTransactions'])->name('user.wallet.transactions');
    });
});

