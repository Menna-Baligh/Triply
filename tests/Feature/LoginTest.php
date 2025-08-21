<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase ;
    /**
     * A basic feature test example.
     */
    public function test_login_returns_token_with_valid_credentials(): void
    {
        //* Create a new fake user in the database using factory
        $user = User::factory()->create();

        //* Send a POST request to the login endpoint with the user's email and the default password
        $reponse = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        //* Assert that the response status is 200 and contains the access_token in the JSON structure
        $reponse->assertStatus(200)
                ->assertJsonStructure(['access_token']);
    }
}
