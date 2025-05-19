<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\LocationController
 */
final class LocationControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $locations = Location::factory()->count(3)->create();

        $response = $this->get(route('locations.index'));

        $response->assertOk();
        $response->assertViewIs('location.index');
        $response->assertViewHas('locations');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('locations.create'));

        $response->assertOk();
        $response->assertViewIs('location.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\LocationController::class,
            'store',
            \App\Http\Requests\LocationControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $address = fake()->word();
        $latitude = fake()->latitude();
        $longitude = fake()->longitude();

        $response = $this->post(route('locations.store'), [
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $locations = Location::query()
            ->where('address', $address)
            ->where('latitude', $latitude)
            ->where('longitude', $longitude)
            ->get();
        $this->assertCount(1, $locations);
        $location = $locations->first();

        $response->assertRedirect(route('locations.index'));
        $response->assertSessionHas('location.id', $location->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $location = Location::factory()->create();

        $response = $this->get(route('locations.show', $location));

        $response->assertOk();
        $response->assertViewIs('location.show');
        $response->assertViewHas('location');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $location = Location::factory()->create();

        $response = $this->get(route('locations.edit', $location));

        $response->assertOk();
        $response->assertViewIs('location.edit');
        $response->assertViewHas('location');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\LocationController::class,
            'update',
            \App\Http\Requests\LocationControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $location = Location::factory()->create();
        $address = fake()->word();
        $latitude = fake()->latitude();
        $longitude = fake()->longitude();

        $response = $this->put(route('locations.update', $location), [
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $location->refresh();

        $response->assertRedirect(route('locations.index'));
        $response->assertSessionHas('location.id', $location->id);

        $this->assertEquals($address, $location->address);
        $this->assertEquals($latitude, $location->latitude);
        $this->assertEquals($longitude, $location->longitude);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $location = Location::factory()->create();

        $response = $this->delete(route('locations.destroy', $location));

        $response->assertRedirect(route('locations.index'));

        $this->assertSoftDeleted($location);
    }
}
