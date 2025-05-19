<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ActivityController
 */
final class ActivityControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $activities = Activity::factory()->count(3)->create();

        $response = $this->get(route('activities.index'));

        $response->assertOk();
        $response->assertViewIs('activity.index');
        $response->assertViewHas('activities');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('activities.create'));

        $response->assertOk();
        $response->assertViewIs('activity.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ActivityController::class,
            'store',
            \App\Http\Requests\ActivityControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $area_id = fake()->randomNumber();
        $name = fake()->name();
        $state = fake()->randomElement(/** enum_attributes **/);
        $priority = fake()->randomElement(/** enum_attributes **/);
        $start_date = Carbon::parse(fake()->dateTime());

        $response = $this->post(route('activities.store'), [
            'area_id' => $area_id,
            'name' => $name,
            'state' => $state,
            'priority' => $priority,
            'start_date' => $start_date->toDateTimeString(),
        ]);

        $activities = Activity::query()
            ->where('area_id', $area_id)
            ->where('name', $name)
            ->where('state', $state)
            ->where('priority', $priority)
            ->where('start_date', $start_date)
            ->get();
        $this->assertCount(1, $activities);
        $activity = $activities->first();

        $response->assertRedirect(route('activities.index'));
        $response->assertSessionHas('activity.id', $activity->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $activity = Activity::factory()->create();

        $response = $this->get(route('activities.show', $activity));

        $response->assertOk();
        $response->assertViewIs('activity.show');
        $response->assertViewHas('activity');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $activity = Activity::factory()->create();

        $response = $this->get(route('activities.edit', $activity));

        $response->assertOk();
        $response->assertViewIs('activity.edit');
        $response->assertViewHas('activity');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ActivityController::class,
            'update',
            \App\Http\Requests\ActivityControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $activity = Activity::factory()->create();
        $area_id = fake()->randomNumber();
        $name = fake()->name();
        $state = fake()->randomElement(/** enum_attributes **/);
        $priority = fake()->randomElement(/** enum_attributes **/);
        $start_date = Carbon::parse(fake()->dateTime());

        $response = $this->put(route('activities.update', $activity), [
            'area_id' => $area_id,
            'name' => $name,
            'state' => $state,
            'priority' => $priority,
            'start_date' => $start_date->toDateTimeString(),
        ]);

        $activity->refresh();

        $response->assertRedirect(route('activities.index'));
        $response->assertSessionHas('activity.id', $activity->id);

        $this->assertEquals($area_id, $activity->area_id);
        $this->assertEquals($name, $activity->name);
        $this->assertEquals($state, $activity->state);
        $this->assertEquals($priority, $activity->priority);
        $this->assertEquals($start_date->timestamp, $activity->start_date);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $activity = Activity::factory()->create();

        $response = $this->delete(route('activities.destroy', $activity));

        $response->assertRedirect(route('activities.index'));

        $this->assertSoftDeleted($activity);
    }
}
