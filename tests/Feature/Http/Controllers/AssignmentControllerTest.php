<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AssignmentController
 */
final class AssignmentControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $assignments = Assignment::factory()->count(3)->create();

        $response = $this->get(route('assignments.index'));

        $response->assertOk();
        $response->assertViewIs('assignment.index');
        $response->assertViewHas('assignments');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('assignments.create'));

        $response->assertOk();
        $response->assertViewIs('assignment.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AssignmentController::class,
            'store',
            \App\Http\Requests\AssignmentControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $intern_id = fake()->randomNumber();
        $role = fake()->word();
        $assigned_date = Carbon::parse(fake()->date());

        $response = $this->post(route('assignments.store'), [
            'intern_id' => $intern_id,
            'role' => $role,
            'assigned_date' => $assigned_date->toDateString(),
        ]);

        $assignments = Assignment::query()
            ->where('intern_id', $intern_id)
            ->where('role', $role)
            ->where('assigned_date', $assigned_date)
            ->get();
        $this->assertCount(1, $assignments);
        $assignment = $assignments->first();

        $response->assertRedirect(route('assignments.index'));
        $response->assertSessionHas('assignment.id', $assignment->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $assignment = Assignment::factory()->create();

        $response = $this->get(route('assignments.show', $assignment));

        $response->assertOk();
        $response->assertViewIs('assignment.show');
        $response->assertViewHas('assignment');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $assignment = Assignment::factory()->create();

        $response = $this->get(route('assignments.edit', $assignment));

        $response->assertOk();
        $response->assertViewIs('assignment.edit');
        $response->assertViewHas('assignment');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AssignmentController::class,
            'update',
            \App\Http\Requests\AssignmentControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $assignment = Assignment::factory()->create();
        $intern_id = fake()->randomNumber();
        $role = fake()->word();
        $assigned_date = Carbon::parse(fake()->date());

        $response = $this->put(route('assignments.update', $assignment), [
            'intern_id' => $intern_id,
            'role' => $role,
            'assigned_date' => $assigned_date->toDateString(),
        ]);

        $assignment->refresh();

        $response->assertRedirect(route('assignments.index'));
        $response->assertSessionHas('assignment.id', $assignment->id);

        $this->assertEquals($intern_id, $assignment->intern_id);
        $this->assertEquals($role, $assignment->role);
        $this->assertEquals($assigned_date, $assignment->assigned_date);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $assignment = Assignment::factory()->create();

        $response = $this->delete(route('assignments.destroy', $assignment));

        $response->assertRedirect(route('assignments.index'));

        $this->assertSoftDeleted($assignment);
    }
}
