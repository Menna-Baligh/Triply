<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Travel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase ;
    /**
     * A basic feature test example.
     */
    public function test_public_user_cannot_access_adding_tour(): void
    {
        //* create a travel record
        $travel = Travel::factory()->create();

        //* try to add a tour without authentication (public user)
        $response = $this->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');

        //* expect 401 Unauthorized response
        $response->assertStatus(401);
    }
}
