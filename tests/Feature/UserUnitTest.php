<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserUnitTest extends TestCase
{

    /** @test */
    public function it_can_find_a_user()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('user.details'));
        $response->assertStatus(200);
        $result = $response->decodeResponseJson();

        $this->assertEquals($this->data['name'], $result['result']['data']['name']);
        $this->assertEquals($this->data['email'], $result['result']['data']['email']);

    }

    /** @test */
    public function it_fails_when_the_user_is_not_authenticated()
    {
        $token = '1212';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('user.details'));
        $response->assertStatus(401);

    }


}
