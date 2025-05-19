<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\InternType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\InternTypeController
 */
final class InternTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $internTypes = InternType::factory()->count(3)->create();

        $response = $this->get(route('intern-types.index'));

        $response->assertOk();
        $response->assertViewIs('internType.index');
        $response->assertViewHas('internTypes');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('intern-types.create'));

        $response->assertOk();
        $response->assertViewIs('internType.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InternTypeController::class,
            'store',
            \App\Http\Requests\InternTypeControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();

        $response = $this->post(route('intern-types.store'), [
            'name' => $name,
        ]);

        $internTypes = InternType::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $internTypes);
        $internType = $internTypes->first();

        $response->assertRedirect(route('internTypes.index'));
        $response->assertSessionHas('internType.id', $internType->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $internType = InternType::factory()->create();

        $response = $this->get(route('intern-types.show', $internType));

        $response->assertOk();
        $response->assertViewIs('internType.show');
        $response->assertViewHas('internType');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $internType = InternType::factory()->create();

        $response = $this->get(route('intern-types.edit', $internType));

        $response->assertOk();
        $response->assertViewIs('internType.edit');
        $response->assertViewHas('internType');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InternTypeController::class,
            'update',
            \App\Http\Requests\InternTypeControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $internType = InternType::factory()->create();
        $name = fake()->name();

        $response = $this->put(route('intern-types.update', $internType), [
            'name' => $name,
        ]);

        $internType->refresh();

        $response->assertRedirect(route('internTypes.index'));
        $response->assertSessionHas('internType.id', $internType->id);

        $this->assertEquals($name, $internType->name);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $internType = InternType::factory()->create();

        $response = $this->delete(route('intern-types.destroy', $internType));

        $response->assertRedirect(route('internTypes.index'));

        $this->assertSoftDeleted($internType);
    }
}
