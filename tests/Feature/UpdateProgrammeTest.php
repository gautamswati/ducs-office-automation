<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Programme;
use App\Models\User;
use App\Types\ProgrammeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProgrammeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function programme_code_can_be_updates()
    {
        $this->withoutExceptionHandling()
            ->signIn(create(User::class), 'admin');

        $programme = create(Programme::class);

        $response = $this->patch(route('staff.programmes.update', $programme), [
            'code' => $newCode = 'New123',
        ])->assertRedirect()
        ->assertSessionHasFlash('success', 'Programme updated successfully!');

        $this->assertEquals(1, Programme::count());
        $this->assertEquals($newCode, $programme->fresh()->code);
    }

    /** @test */
    public function programme_is_not_validated_for_uniqueness_if_code_is_not_changed()
    {
        $this->withoutExceptionHandling()
            ->signIn();

        $programme = create(Programme::class);

        $response = $this->patch(route('staff.programmes.update', $programme), [
            'code' => $programme->code,
            'name' => $newName = 'New Programme',
        ])->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHasFlash('success', 'Programme updated successfully!');

        $this->assertEquals(1, Programme::count());
        $this->assertEquals($newName, $programme->fresh()->name);
    }

    /** @test */
    public function type_field_can_be_updated()
    {
        $this->withoutExceptionHandling()
            ->signIn();

        $programme = create(Programme::class, 1, ['type' => ProgrammeType::UNDER_GRADUATE]);

        $response = $this->patch(route('staff.programmes.update', $programme), [
            'type' => ProgrammeType::POST_GRADUATE,
        ])->assertRedirect()
        ->assertSessionHasFlash('success', 'Programme updated successfully!');

        $this->assertEquals(1, Programme::count());
        $this->assertEquals(ProgrammeType::POST_GRADUATE, $programme->fresh()->type);
    }
}
