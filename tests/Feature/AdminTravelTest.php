<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_non_admin_user_cannot_access_adding_travel(): void
    {
        //* Seed roles into the database (so the 'editor' role exists)
        $this->seed(RoleSeeder::class);

        //* Create a normal user
        $user = User::factory()->create();

        //* Assign the 'editor' role to the user
        $user->roles()->attach(Role::where('name','editor')->value('id'));

        //* Try to access the "add travel" endpoint as this user
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');

        //* Assert that the response status is 403 (Forbidden) since the user is not an admin
        $response->assertStatus(403);
    }
}
