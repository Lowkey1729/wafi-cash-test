<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    protected $faker;
    protected $user;
    protected array $data;


    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
    }

    protected function authenticate()
    {
        $name = $this->faker->name;
        $email = $this->faker->email;
        $this->data = [
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('secret1234'),
            'email_verified_at' => "2022-07-07 09:54:59"
        ];
        $user = User::query()->create($this->data);
        $this->user = $user;
        //Send post request
        $response = $this->json('POST', route('auth.login'), [
            'email' => $email,
            'password' => 'secret1234',
        ])->decodeResponseJson();
        return $response['token'];
    }

    /**
     * Reset the migrations
     */
    public function tearDown(): void
    {
        $this->artisan('migrate:reset');
        parent::tearDown();

    }
}
