<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WalletUnitTest extends TestCase
{
    /** @test */
    public function it_cannot_find_user_wallet()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('user.wallet.details'));
        $response->assertStatus(404);

    }

    /** @test */
    public function it_does_not_permit_to_access_this_resource_when_not_authenticated()
    {
        $token = '1212';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('user.wallet.details'));
        $response->assertStatus(401);

    }

    /** @test */
    public function it_can_find_user_wallet()
    {
        $token = $this->authenticate();
        $this->createWallet($this->user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('user.wallet.details'));
        $response->assertStatus(200);
        $this->user->wallet()->delete();
        $this->user->delete();

    }

    /** @test */
    public function it_can_find_wallet_account_number()
    {
        $token = $this->authenticate();
        $this->createWallet($this->user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('user.wallet.details'));
        $response->assertStatus(200);

        $this->assertNotNull($response->decodeResponseJson()['result']['data']['account_number']);
        $this->user->wallet()->delete();
        $this->user->delete();

    }

    /** @test */
    public function it_can_fund_wallet()
    {
        $token = $this->authenticate();

        //Virtual User
        //Virtual User
        $user_b = $this->createNewUser('wallet-service', 'wallet_service@gmail.com');
        //Virtual Wallet

        $this->createWallet($user_b, 0, true);
        $this->createWallet($this->user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('user.wallet.fund'), [
            'amount' => 100
        ]);
        $response->assertStatus(200);


    }

    /** @test */
    public function it_can_transfer_from_wallet_to_wallet()
    {
        $token = $this->authenticate();

        //Virtual User
        $new_user = $this->createNewUser('User B', 'userb@gmail.com');
        //Virtual Wallet
        $wallet = $this->createWallet($new_user);
        $this->createWallet($this->user, 1200);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('user.wallet.wallet-to-wallet'), [
            'amount' => 100,
            'account_number' => $wallet->account_number
        ]);
        $response->assertStatus(200);


    }

    /** @test */
    public function it_cannot_transfer_amount_below_the_available_balance()
    {
        $token = $this->authenticate();
        //Virtual User
        $new_user = $this->createNewUser('wallet-service', 'wallet_service@gmail.com');
        //Virtual Wallet

        $user_b_wallet = $this->createWallet($new_user, 0, true);
        $this->createWallet($this->user);


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('user.wallet.wallet-to-wallet'), [
            'amount' => 100,
            'account_number' => $user_b_wallet->account_number
        ]);
        $response->assertStatus(402);


    }

    /** @test */
    public function it_can_update_wallet_balance_after_wallet_to_wallet_transfer()
    {
        $token = $this->authenticate();

        //Virtual User
        $new_user = $this->createNewUser('User B', 'userb@gmail.com');
        //Virtual Wallet
        $new_user_balance = 500;
        $auth_user_balance = 1200;
        $user_b_wallet = $this->createWallet($new_user, $new_user_balance, true);
        $this->createWallet($this->user, $auth_user_balance);


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('user.wallet.wallet-to-wallet'), [
            'amount' => $amount = 100,
            'account_number' => $user_b_wallet->account_number
        ]);

        $response->assertStatus(200);
        $this->assertEquals(($new_user_balance + $amount), $response['result']['data']['transfer_to']['balance']);
        $this->assertEquals(($auth_user_balance - $amount), $response['result']['data']['transfer_from']['balance']);


    }

    protected function createWallet($user, $balance = 0, $isVirtual = false)
    {
        return $user->wallet()->create([
            'balance' => $balance,
            'status' => 'ACTIVE',
            'is_virtual' => $isVirtual,
        ]);
    }

    protected function createNewUser($name, $email)
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('secret1234'),
            'email_verified_at' => "2022-07-07 09:54:59"
        ]);
    }


}
