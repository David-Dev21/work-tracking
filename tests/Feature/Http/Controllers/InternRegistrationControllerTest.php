<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\InternRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\InternRegistrationController
 */
final class InternRegistrationControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $internRegistrations = InternRegistration::factory()->count(3)->create();

        $response = $this->get(route('intern-registrations.index'));

        $response->assertOk();
        $response->assertViewIs('internRegistration.index');
        $response->assertViewHas('internRegistrations');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('intern-registrations.create'));

        $response->assertOk();
        $response->assertViewIs('internRegistration.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InternRegistrationController::class,
            'store',
            \App\Http\Requests\InternRegistrationControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $intern_type_id = fake()->randomNumber();
        $intern_id = fake()->randomNumber();
        $area_id = fake()->randomNumber();
        $start_date = Carbon::parse(fake()->date());
        $end_date = Carbon::parse(fake()->date());

        $response = $this->post(route('intern-registrations.store'), [
            'intern_type_id' => $intern_type_id,
            'intern_id' => $intern_id,
            'area_id' => $area_id,
            'start_date' => $start_date->toDateString(),
            'end_date' => $end_date->toDateString(),
        ]);

        $internRegistrations = InternRegistration::query()
            ->where('intern_type_id', $intern_type_id)
            ->where('intern_id', $intern_id)
            ->where('area_id', $area_id)
            ->where('start_date', $start_date)
            ->where('end_date', $end_date)
            ->get();
        $this->assertCount(1, $internRegistrations);
        $internRegistration = $internRegistrations->first();

        $response->assertRedirect(route('internRegistrations.index'));
        $response->assertSessionHas('internRegistration.id', $internRegistration->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $internRegistration = InternRegistration::factory()->create();

        $response = $this->get(route('intern-registrations.show', $internRegistration));

        $response->assertOk();
        $response->assertViewIs('internRegistration.show');
        $response->assertViewHas('internRegistration');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $internRegistration = InternRegistration::factory()->create();

        $response = $this->get(route('intern-registrations.edit', $internRegistration));

        $response->assertOk();
        $response->assertViewIs('internRegistration.edit');
        $response->assertViewHas('internRegistration');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InternRegistrationController::class,
            'update',
            \App\Http\Requests\InternRegistrationControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $internRegistration = InternRegistration::factory()->create();
        $intern_type_id = fake()->randomNumber();
        $intern_id = fake()->randomNumber();
        $area_id = fake()->randomNumber();
        $start_date = Carbon::parse(fake()->date());
        $end_date = Carbon::parse(fake()->date());

        $response = $this->put(route('intern-registrations.update', $internRegistration), [
            'intern_type_id' => $intern_type_id,
            'intern_id' => $intern_id,
            'area_id' => $area_id,
            'start_date' => $start_date->toDateString(),
            'end_date' => $end_date->toDateString(),
        ]);

        $internRegistration->refresh();

        $response->assertRedirect(route('internRegistrations.index'));
        $response->assertSessionHas('internRegistration.id', $internRegistration->id);

        $this->assertEquals($intern_type_id, $internRegistration->intern_type_id);
        $this->assertEquals($intern_id, $internRegistration->intern_id);
        $this->assertEquals($area_id, $internRegistration->area_id);
        $this->assertEquals($start_date, $internRegistration->start_date);
        $this->assertEquals($end_date, $internRegistration->end_date);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $internRegistration = InternRegistration::factory()->create();

        $response = $this->delete(route('intern-registrations.destroy', $internRegistration));

        $response->assertRedirect(route('internRegistrations.index'));

        $this->assertSoftDeleted($internRegistration);
    }
}
