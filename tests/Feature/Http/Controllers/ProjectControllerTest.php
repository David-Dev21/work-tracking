<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProjectController
 */
final class ProjectControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $projects = Project::factory()->count(3)->create();

        $response = $this->get(route('projects.index'));

        $response->assertOk();
        $response->assertViewIs('project.index');
        $response->assertViewHas('projects');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('projects.create'));

        $response->assertOk();
        $response->assertViewIs('project.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProjectController::class,
            'store',
            \App\Http\Requests\ProjectControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $area_id = fake()->randomNumber();
        $name = fake()->name();
        $state = fake()->randomElement(/** enum_attributes **/);
        $start_date = Carbon::parse(fake()->dateTime());

        $response = $this->post(route('projects.store'), [
            'area_id' => $area_id,
            'name' => $name,
            'state' => $state,
            'start_date' => $start_date->toDateTimeString(),
        ]);

        $projects = Project::query()
            ->where('area_id', $area_id)
            ->where('name', $name)
            ->where('state', $state)
            ->where('start_date', $start_date)
            ->get();
        $this->assertCount(1, $projects);
        $project = $projects->first();

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('project.id', $project->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.show', $project));

        $response->assertOk();
        $response->assertViewIs('project.show');
        $response->assertViewHas('project');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.edit', $project));

        $response->assertOk();
        $response->assertViewIs('project.edit');
        $response->assertViewHas('project');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProjectController::class,
            'update',
            \App\Http\Requests\ProjectControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $project = Project::factory()->create();
        $area_id = fake()->randomNumber();
        $name = fake()->name();
        $state = fake()->randomElement(/** enum_attributes **/);
        $start_date = Carbon::parse(fake()->dateTime());

        $response = $this->put(route('projects.update', $project), [
            'area_id' => $area_id,
            'name' => $name,
            'state' => $state,
            'start_date' => $start_date->toDateTimeString(),
        ]);

        $project->refresh();

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('project.id', $project->id);

        $this->assertEquals($area_id, $project->area_id);
        $this->assertEquals($name, $project->name);
        $this->assertEquals($state, $project->state);
        $this->assertEquals($start_date->timestamp, $project->start_date);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $project = Project::factory()->create();

        $response = $this->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));

        $this->assertSoftDeleted($project);
    }
}
