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
}
