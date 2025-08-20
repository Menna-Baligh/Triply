<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TourListTest extends TestCase
{
    use RefreshDatabase ;
    /**
     * A basic feature test example.
     */
    public function test_tours_list_by_travel_slug_returns_correct_tours(): void
    {
        //* Create a new Travel record using the factory
        $travel  = Travel::factory()->create();

        //* Create a Tour that belongs to the created Travel
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);

        //* Send a GET request to the endpoint that lists tours for the given travel slug
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        //* Assert that the response status code is 200 (success)
        $response->assertStatus(200);

        //* Assert that the response contains exactly 1 tour in the "data" array
        $response->assertJsonCount(1,'data');

        //* Assert that the returned JSON contains a tour with the correct ID
        $response->assertJsonFragment([
            'id' => $tour->id,
        ]);
    }
}
