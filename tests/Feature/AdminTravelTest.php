<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Travel;
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
    public function test_saves_travel_successfully_with_valid_data(){
        //* Seed roles into the database (so the 'admin' role exists)
        $this->seed(RoleSeeder::class);

        //* Create an admin user
        $admin = User::factory()->create();

        //* Assign the 'admin' role to the user
        $admin->roles()->attach(Role::where('name','admin')->value('id'));

        //* First attempt: send incomplete travel data (should fail with 422 - validation error)
        $response = $this->actingAs($admin)->postJson('/api/v1/admin/travels', [
            'name' => 'Test Travel',
        ]);
        $response->assertStatus(422);

        //* Second attempt: send complete valid travel data (should succeed with 201 - created)
        $response = $this->actingAs($admin)->postJson('/api/v1/admin/travels', [
            'name' => 'Test Travel',
            'is_public' => 1,
            'description' => 'This is a test travel description',
            'number_of_days' => 5,
        ]);
        $response->assertStatus(201);

        //* Finally: check if the created travel appears in the public travels list
        $response = $this->get('/api/v1/travels');
        $response->assertJsonFragment(['name' => 'Test Travel']);
    }
    public function test_updates_travel_successfully_with_valid_data(){
        //* Seed roles into the database (so the 'admin' role exists)
        $this->seed(RoleSeeder::class);

        //* Create an admin user
        $editor = User::factory()->create();

        //* Assign the 'editor' role to the user
        $editor->roles()->attach(Role::where('name','editor')->value('id'));

        //* create travel record
        $travel = Travel::factory()->create();

        //* First attempt: send incomplete travel data (should fail with 422 - validation error)
        $response = $this->actingAs($editor)->putJson('/api/v1/admin/travels/' .$travel->id,[
            'name' => 'Updated Travel Name',
        ]);
        $response->assertStatus(422);

        //* Second attempt: send complete valid travel data (should succeed with 201 - created)
        $response = $this->actingAs($editor)->putJson('/api/v1/admin/travels/' .$travel->id,[
            'name' => 'Updated Travel Name',
            'is_public' => 1,
            'description' => 'This is an updated travel description',
            'number_of_days' => 7,
        ]);
        $response->assertStatus(200);

        //* Finally: check if the updated travel appears in the public travels list
        $response = $this->get('/api/v1/travels');
        $response->assertJsonFragment(['name' => 'Updated Travel Name']);
    }
}
