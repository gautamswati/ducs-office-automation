<?php

namespace Tests\Feature;

use App\Models\IncomingLetter;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditIncomingLettersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_not_edit_any_incoming_letter()
    {
        $this->expectException(AuthenticationException::class);

        $letter = create(IncomingLetter::class);

        $this->withoutExceptionHandling()
            ->get(route('staff.incoming_letters.edit', $letter))
            ->assertRedirect();
    }

    /** @test */
    public function user_cannot_edit_incoming_letter_if_permission_not_given()
    {
        $user = create(User::class);
        $role = Role::create(['name' => 'random role']);
        $permission = Permission::firstOrCreate(['name' => 'edit incoming letters']);

        $letter = create(IncomingLetter::class, 1, [
            'subject' => $oldSubject = 'old subject',
        ]);

        $role->revokePermissionTo($permission);

        $this->signIn($user, $role->name);

        $this->withExceptionHandling()
            ->get(route('staff.incoming_letters.edit', $letter))
            ->assertForbidden();

        $this->withExceptionHandling()
            ->patch(route('staff.incoming_letters.update', $letter), ['subject' => 'new subject'])
            ->assertForbidden();

        $this->assertEquals($oldSubject, $letter->fresh()->subject);
    }

    /** @test */
    public function user_cannot_edit_incoming_letter_if_they_did_not_create_it_even_if_they_have_permission()
    {
        $user = create(User::class);
        $role = Role::create(['name' => 'random role']);
        $permission = Permission::firstOrCreate(['name' => 'edit incoming letters']);

        $letter = create(incomingLetter::class, 1, [
            'subject' => $oldSubject = 'old subject',
            'creator_id' => create(User::class)->id,
        ]);

        $role->givePermissionTo($permission);

        $this->signIn($user, $role->name);

        $this->withExceptionHandling()
            ->get(route('staff.incoming_letters.edit', $letter))
            ->assertForbidden();

        $this->withExceptionHandling()
            ->patch(route('staff.incoming_letters.update', $letter), ['subject' => 'new subject'])
            ->assertForbidden();

        $this->assertEquals($oldSubject, $letter->fresh()->subject);
    }
}
