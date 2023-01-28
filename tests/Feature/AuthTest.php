<?php

namespace Tests\Feature;

use App\Exceptions\CreateUserInvalidArgumentException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * Test registration
     */
    public function it_can_register_user()
    {
        //User's data
        $data = [
            'email' => 'olarewajumojeed9@gmail.com',
            'name' => 'test',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];


        //Send post request
        $response = $this->json('POST', route('auth.register'), $data);

        //Assert it was successful
        $response->assertStatus(200);

        //Delete data
        $user = User::query()->where('email', 'olarewajumojeed9@gmail.com')->first();
        $user->wallet->delete();
        $user->delete();
    }

    /**
     * @test
     *
     */
    public function it_validates_fields_when_registering()
    {
        //User's data
        $data = [
            'email' => $this->faker->name,
            'name' => $this->faker->email,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];




        //Send post request
        $response = $this->json('POST', route('auth.register'), $data);

        //Assert it passed through validation.
        $response->assertStatus(422);
    }

    /**
     * @test
     *
     */
    public function it_does_not_validate_fields_when_registering()
    {
        //User's data
        $data = [
            'email' => $this->faker->name,
            'name' => $this->faker->email,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];




        //Send post request
        $response = $this->json('POST', route('auth.register'), $data);

        //Assert it passed through validation.
        $response->assertStatus(200);
    }

    /**
     * @test
     *
     */
    public function it_can_login_user()
    {
        //User's data
        User::query()->create([
            'email' => 'test@gmail.com',
            'name' => 'test',
            'password' => bcrypt('secret1234'),
            'email_verified_at' => "2022-07-07 09:54:59"
        ]);

        //Send post request
        $response = $this->json('POST', route('auth.login'), [
            'email' => 'test@gmail.com',
            'password' => 'secret1234',
        ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
    }


}
