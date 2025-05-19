<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Responsible;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ResponsibleController
 */
final class ResponsibleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $responsibles = Responsible::factory()->count(3)->create();

        $response = $this->get(route('responsibles.index'));

        $response->assertOk();
        $response->assertViewIs('responsible.index');
        $response->assertViewHas('responsibles');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('responsibles.create'));

        $response->assertOk();
        $response->assertViewIs('responsible.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ResponsibleController::class,
            'store',
            \App\Http\Requests\ResponsibleControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $area_role_id = fake()->randomNumber();
        $academic_degree = fake()->randomNumber();
        $name = fake()->name();
        $last_name = fake()->lastName();
        $identity_card = fake()->word();

        $response = $this->post(route('responsibles.store'), [
            'area_role_id' => $area_role_id,
            'academic_degree' => $academic_degree,
            'name' => $name,
            'last_name' => $last_name,
            'identity_card' => $identity_card,
        ]);

        $responsibles = Responsible::query()
            ->where('area_role_id', $area_role_id)
            ->where('academic_degree', $academic_degree)
            ->where('name', $name)
            ->where('last_name', $last_name)
            ->where('identity_card', $identity_card)
            ->get();
        $this->assertCount(1, $responsibles);
        $responsible = $responsibles->first();

        $response->assertRedirect(route('responsibles.index'));
        $response->assertSessionHas('responsible.id', $responsible->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $responsible = Responsible::factory()->create();

        $response = $this->get(route('responsibles.show', $responsible));

        $response->assertOk();
        $response->assertViewIs('responsible.show');
        $response->assertViewHas('responsible');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $responsible = Responsible::factory()->create();

        $response = $this->get(route('responsibles.edit', $responsible));

        $response->assertOk();
        $response->assertViewIs('responsible.edit');
        $response->assertViewHas('responsible');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ResponsibleController::class,
            'update',
            \App\Http\Requests\ResponsibleControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $responsible = Responsible::factory()->create();
        $area_role_id = fake()->randomNumber();
        $academic_degree = fake()->randomNumber();
        $name = fake()->name();
        $last_name = fake()->lastName();
        $identity_card = fake()->word();

        $response = $this->put(route('responsibles.update', $responsible), [
            'area_role_id' => $area_role_id,
            'academic_degree' => $academic_degree,
            'name' => $name,
            'last_name' => $last_name,
            'identity_card' => $identity_card,
        ]);

        $responsible->refresh();

        $response->assertRedirect(route('responsibles.index'));
        $response->assertSessionHas('responsible.id', $responsible->id);

        $this->assertEquals($area_role_id, $responsible->area_role_id);
        $this->assertEquals($academic_degree, $responsible->academic_degree);
        $this->assertEquals($name, $responsible->name);
        $this->assertEquals($last_name, $responsible->last_name);
        $this->assertEquals($identity_card, $responsible->identity_card);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $responsible = Responsible::factory()->create();

        $response = $this->delete(route('responsibles.destroy', $responsible));

        $response->assertRedirect(route('responsibles.index'));

        $this->assertSoftDeleted($responsible);
    }
}
