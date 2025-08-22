<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_public_user_cannot_access_adding_tour(): void
    {
        // * create a travel record
        $travel = Travel::factory()->create();

        // * try to add a tour without authentication (public user)
        $response = $this->postJson('/api/v1/admin/travels/'.$travel->id.'/tours');

        // * expect 401 Unauthorized response
        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_tour(): void
    {
        // * seed roles into the database
        $this->seed(RoleSeeder::class);

        // * create a user and assign them the "editor" role (not admin)
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        // * create a travel record
        $travel = Travel::factory()->create();

        // * try to add a tour as a non-admin user
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours');

        // * expect 403 Forbidden response
        $response->assertStatus(403);
    }

    public function test_saves_tour_successfully_with_valid_data(): void
    {
        // * seed roles into the database
        $this->seed(RoleSeeder::class);

        // * create a user and assign them the "admin" role
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        // * create travel record
        $travel = Travel::factory()->create();

        // * Try creating a tour with missing required fields (should fail)
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours', [
            'name' => 'Amazing Tour',
        ]);
        $response->assertStatus(422); // * validation error expected

        // * Create a tour with all required fields (should succeed)
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours', [
            'name' => 'Amazing Tour',
            'starting_date' => '2023-10-01',
            'ending_date' => '2023-10-05',
            'price' => 100.00,
        ]);

        $response->assertStatus(201); // * tour successfully created

        // * Fetch tours of the travel and check that the new tour exists
        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');
        $response->assertJsonFragment(['name' => 'Amazing Tour']);
    }
}
