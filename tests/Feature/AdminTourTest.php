<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Travel;
use Database\Seeders\RoleSeeder;
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
    public function test_non_admin_user_cannot_access_adding_tour(): void
    {
        //* seed roles into the database
        $this->seed(RoleSeeder::class);

        //* create a user and assign them the "editor" role (not admin)
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name','editor')->value('id'));

        //* create a travel record
        $travel = Travel::factory()->create();

        //* try to add a tour as a non-admin user
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');

        //* expect 403 Forbidden response
        $response->assertStatus(403);
    }
}
