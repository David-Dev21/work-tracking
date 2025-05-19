<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Intern;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\InternController
 */
final class InternControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $interns = Intern::factory()->count(3)->create();

        $response = $this->get(route('interns.index'));

        $response->assertOk();
        $response->assertViewIs('intern.index');
        $response->assertViewHas('interns');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('interns.create'));

        $response->assertOk();
        $response->assertViewIs('intern.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InternController::class,
            'store',
            \App\Http\Requests\InternControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();
        $last_name = fake()->lastName();
        $identity_card = fake()->word();
        $university_registration = fake()->word();

        $response = $this->post(route('interns.store'), [
            'name' => $name,
            'last_name' => $last_name,
            'identity_card' => $identity_card,
            'university_registration' => $university_registration,
        ]);

        $interns = Intern::query()
            ->where('name', $name)
            ->where('last_name', $last_name)
            ->where('identity_card', $identity_card)
            ->where('university_registration', $university_registration)
            ->get();
        $this->assertCount(1, $interns);
        $intern = $interns->first();

        $response->assertRedirect(route('interns.index'));
        $response->assertSessionHas('intern.id', $intern->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $intern = Intern::factory()->create();

        $response = $this->get(route('interns.show', $intern));

        $response->assertOk();
        $response->assertViewIs('intern.show');
        $response->assertViewHas('intern');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $intern = Intern::factory()->create();

        $response = $this->get(route('interns.edit', $intern));

        $response->assertOk();
        $response->assertViewIs('intern.edit');
        $response->assertViewHas('intern');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InternController::class,
            'update',
            \App\Http\Requests\InternControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $intern = Intern::factory()->create();
        $name = fake()->name();
        $last_name = fake()->lastName();
        $identity_card = fake()->word();
        $university_registration = fake()->word();

        $response = $this->put(route('interns.update', $intern), [
            'name' => $name,
            'last_name' => $last_name,
            'identity_card' => $identity_card,
            'university_registration' => $university_registration,
        ]);

        $intern->refresh();

        $response->assertRedirect(route('interns.index'));
        $response->assertSessionHas('intern.id', $intern->id);

        $this->assertEquals($name, $intern->name);
        $this->assertEquals($last_name, $intern->last_name);
        $this->assertEquals($identity_card, $intern->identity_card);
        $this->assertEquals($university_registration, $intern->university_registration);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $intern = Intern::factory()->create();

        $response = $this->delete(route('interns.destroy', $intern));

        $response->assertRedirect(route('interns.index'));

        $this->assertSoftDeleted($intern);
    }
}
