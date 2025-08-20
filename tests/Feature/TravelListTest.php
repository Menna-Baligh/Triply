<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Travel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelListTest extends TestCase
{
    use RefreshDatabase ;
    /**
     * A basic feature test example.
     */
    public function test_travels_list_returns_paginated_data_correctly(): void
    {
        //* Create 16 Travel records using the factory, all marked as public
        Travel::factory(16)->create(['is_public' => true]);

        //* Send a GET request to the travels API endpoint
        $response = $this->get('/api/v1/travels');

        //* Assert that the response status code is 200 (OK)
        $response->assertStatus(200);

        //* Assert that the response JSON has exactly 15 items inside "data"
        $response->assertJsonCount(15 , 'data');

        //* Assert that the "meta.last_page" field in the JSON equals 2 (pagination works correctly)
        $response->assertJsonPath('meta.last_page', 2);
    }
    public function test_travels_list_shows_public_records_only(){
        //* Create a travel record that is public
        $publicTravel = Travel::factory()->create(['is_public' => true]);

        //* Create another travel record that is private (not public)
        Travel::factory()->create(['is_public' => false]);

        //* Send a GET request to the travels API endpoint
        $response = $this->get('/api/v1/travels');

        //* Assert that the response status code is 200 (OK)
        $response->assertStatus(200);

        //* Assert that the JSON response contains exactly 1 record in the "data" array
        $response->assertJsonCount(1, 'data');

        //* Assert that the returned travel's name matches the public travel we created
        $response->assertJsonPath('data.0.name', $publicTravel->name);
    }
}
