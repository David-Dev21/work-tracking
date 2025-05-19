<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AreaRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AreaRoleController
 */
final class AreaRoleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $areaRoles = AreaRole::factory()->count(3)->create();

        $response = $this->get(route('area-roles.index'));

        $response->assertOk();
        $response->assertViewIs('areaRole.index');
        $response->assertViewHas('areaRoles');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('area-roles.create'));

        $response->assertOk();
        $response->assertViewIs('areaRole.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AreaRoleController::class,
            'store',
            \App\Http\Requests\AreaRoleControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();
        $area_id = fake()->randomNumber();

        $response = $this->post(route('area-roles.store'), [
            'name' => $name,
            'area_id' => $area_id,
        ]);

        $areaRoles = AreaRole::query()
            ->where('name', $name)
            ->where('area_id', $area_id)
            ->get();
        $this->assertCount(1, $areaRoles);
        $areaRole = $areaRoles->first();

        $response->assertRedirect(route('areaRoles.index'));
        $response->assertSessionHas('areaRole.id', $areaRole->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $areaRole = AreaRole::factory()->create();

        $response = $this->get(route('area-roles.show', $areaRole));

        $response->assertOk();
        $response->assertViewIs('areaRole.show');
        $response->assertViewHas('areaRole');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $areaRole = AreaRole::factory()->create();

        $response = $this->get(route('area-roles.edit', $areaRole));

        $response->assertOk();
        $response->assertViewIs('areaRole.edit');
        $response->assertViewHas('areaRole');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AreaRoleController::class,
            'update',
            \App\Http\Requests\AreaRoleControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $areaRole = AreaRole::factory()->create();
        $name = fake()->name();
        $area_id = fake()->randomNumber();

        $response = $this->put(route('area-roles.update', $areaRole), [
            'name' => $name,
            'area_id' => $area_id,
        ]);

        $areaRole->refresh();

        $response->assertRedirect(route('areaRoles.index'));
        $response->assertSessionHas('areaRole.id', $areaRole->id);

        $this->assertEquals($name, $areaRole->name);
        $this->assertEquals($area_id, $areaRole->area_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $areaRole = AreaRole::factory()->create();

        $response = $this->delete(route('area-roles.destroy', $areaRole));

        $response->assertRedirect(route('areaRoles.index'));

        $this->assertSoftDeleted($areaRole);
    }
}
