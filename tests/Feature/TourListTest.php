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
    public function test_tour_price_is_shown_correctly(){
        //* Create a new Travel record using the factory
        $travel = Travel::factory()->create();

        //* Create a Tour linked to the above Travel, with a specific price value
        Tour::factory()->create([
                'travel_id' => $travel->id ,
                'price' => 123.45
        ]);

        //* Send a GET request to fetch tours of the created travel using its slug
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        //* Assert that the response status code is 200 (OK)
        $response->assertStatus(200);

        //* Assert that the response JSON contains exactly 1 tour record in 'data'
        $response->assertJsonCount(1, 'data');

        //* Assert that the returned JSON fragment contains the correct price value
        $response->assertJsonFragment([
            'price' => '123.45',
        ]);
    }
    public function test_tours_list_returns_pagination(){
        //* Create a travel record to associate tours with
        $travel = Travel::factory()->create();

        //* Create 16 tours related to the created travel (so pagination can be tested)
        Tour::factory(16)->create(['travel_id' => $travel->id]);

        //* Send GET request to fetch tours list for the travel
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);

        //* Assert that the first page contains 15 tours (default pagination limit)
        $response->assertJsonCount(15, 'data');

        //* Assert that the "last_page" value in pagination meta is 2 (16 tours / 15 per page = 2 pages)
        $response->assertJsonPath('meta.last_page', 2);
    }
    public function test_tours_list_sorts_by_starting_date_correctly(){
        //* Create a travel record to associate tours with
        $travel = Travel::factory()->create();

        //* Create a tour that starts later than the current date
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        //* Create a tour that starts earlier
        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDay()
        ]);

        //* Send GET request to fetch tours list for the travel
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);

        //* Assert that the first tour returned is the one that started earlier
        $response->assertJsonPath('data.0.id', $earlierTour->id);

        //* Assert that the second tour returned is the one that started later
        $response->assertJsonPath('data.1.id', $laterTour->id);
    }
    public function test_tours_list_sorts_by_price_correctly(){
        //* Create a travel record to associate tours with
        $travel = Travel::factory()->create();

        //* Create a tour with a higher price
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200.00,
        ]);

        //* Create a tour with a lower price
        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100.00,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);

        //* Create a tour with the same price but earlier starting date
        $cheapEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100.00,
            'starting_date' => now(),
            'ending_date' => now()->addDay()
        ]);

        //* Send GET request to fetch tours list for the travel
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?sortBy=price&sortOrder=asc');

        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);

        //* Assert that the first tour returned is the one with the lower price and earlier starting date
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);

        //* Assert that the second tour returned is the one with the lower price but later starting date
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);

        //* Assert that the third tour returned is the one with the higher price
        $response->assertJsonPath('data.2.id', $expensiveTour->id);
    }
    public function test_tours_list_filters_by_price_correctly(){
        //* Create a travel record to associate tours with
        $travel = Travel::factory()->create();

        //* create expensive tour
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200.00,
        ]);

        //* create cheap tour
        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100.00,
        ]);

        //* Endpoint for fetching tours by travel slug
        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';

        //* Send GET request to fetch tours with price filter set to 100
        $response = $this->get($endpoint . '?priceFrom=100');
        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);
        //* Assert that the response contains exactly 1 tour in the "data" array
        $response->assertJsonCount(2, 'data');
        //* Assert that the returned JSON contains a tour with the correct ID
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        //* Send GET request to fetch tours with price filter set to 150
        $response = $this->get($endpoint . '?priceFrom=150');
        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);
        //* Assert that the response contains exactly 1 tour in the "data" array
        $response->assertJsonCount(1, 'data');
        //* Assert that the returned JSON contains a tour with the correct ID
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
        $response->assertJsonMissing(['id' => $cheapTour->id]);

        //* Send GET request to fetch tours with price filter set to 250
        $response = $this->get($endpoint . '?priceFrom=250');
        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);
        //* Assert that the response contains exactly 0 tour in the "data" array
        $response->assertJsonCount(0, 'data');

        //* Send GET request to fetch tours with priceTO filter set to 200
        $response = $this->get($endpoint . '?priceTo=200');
        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);
        //* Assert that the response contains exactly 2 tours in the "data" array
        $response->assertJsonCount(2, 'data');
        //* Assert that the returned JSON contains a tour with the correct ID
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        //* Send GET request to fetch tours with priceTO filter set to 150
        $response = $this->get($endpoint . '?priceTo=150');
        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);
        //* Assert that the response contains exactly 1 tour in the "data" array
        $response->assertJsonCount(1, 'data');
        //* Assert that the returned JSON contains a tour with the correct ID
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonMissing(['id' => $expensiveTour->id]);

        //* Send GET request to fetch tours with priceTO filter set to 50
        $response = $this->get($endpoint . '?priceTo=50');
        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);
        //* Assert that the response contains exactly 0 tour in the "data" array
        $response->assertJsonCount(0, 'data');

        //* Send GET request to fetch tours with both priceFrom 150 and priceTo 250 filters
        $response = $this->get($endpoint . '?priceFrom=150&priceTo=250');
        //* Assert that the response returns status code 200 (success)
        $response->assertStatus(200);
        //* Assert that the response contains exactly 1 tour in the "data" array
        $response->assertJsonCount(1, 'data');
        //* Assert that the returned JSON contains a tour with the correct ID
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
        $response->assertJsonMissing(['id' => $cheapTour->id]);
    }
}
