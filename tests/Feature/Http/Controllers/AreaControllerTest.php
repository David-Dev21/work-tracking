<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AreaController
 */
final class AreaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $areas = Area::factory()->count(3)->create();

        $response = $this->get(route('areas.index'));

        $response->assertOk();
        $response->assertViewIs('area.index');
        $response->assertViewHas('areas');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('areas.create'));

        $response->assertOk();
        $response->assertViewIs('area.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AreaController::class,
            'store',
            \App\Http\Requests\AreaControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();

        $response = $this->post(route('areas.store'), [
            'name' => $name,
        ]);

        $areas = Area::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $areas);
        $area = $areas->first();

        $response->assertRedirect(route('areas.index'));
        $response->assertSessionHas('area.id', $area->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $area = Area::factory()->create();

        $response = $this->get(route('areas.show', $area));

        $response->assertOk();
        $response->assertViewIs('area.show');
        $response->assertViewHas('area');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $area = Area::factory()->create();

        $response = $this->get(route('areas.edit', $area));

        $response->assertOk();
        $response->assertViewIs('area.edit');
        $response->assertViewHas('area');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AreaController::class,
            'update',
            \App\Http\Requests\AreaControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $area = Area::factory()->create();
        $name = fake()->name();

        $response = $this->put(route('areas.update', $area), [
            'name' => $name,
        ]);

        $area->refresh();

        $response->assertRedirect(route('areas.index'));
        $response->assertSessionHas('area.id', $area->id);

        $this->assertEquals($name, $area->name);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $area = Area::factory()->create();

        $response = $this->delete(route('areas.destroy', $area));

        $response->assertRedirect(route('areas.index'));

        $this->assertSoftDeleted($area);
    }
}
