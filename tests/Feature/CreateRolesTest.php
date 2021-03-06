<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateRolesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_new_roles_with_permissions()
    {
        $this->signIn($user = create(User::class), 'admin');

        $countRoles = Role::count();

        $permission = Permission::create(['name' => 'special permission']);
        $anotherPermssion = Permission::create(['name' => 'another permission']);

        $this->withoutExceptionHandling()
            ->post(route('staff.roles.store'), [
                'name' => 'special_role',
                'permissions' => [$permission->id, $anotherPermssion->id],
            ])->assertRedirect()
            ->assertSessionHasFlash('success', 'Role created successfully!');

        $this->assertEquals($countRoles + 1, Role::count());

        $newRole = Role::latest()->first();
        $this->assertEquals('special_role', $newRole->name);
        $this->assertTrue(
            $newRole->hasAllPermissions($permission, $anotherPermssion)
        );
    }
}
