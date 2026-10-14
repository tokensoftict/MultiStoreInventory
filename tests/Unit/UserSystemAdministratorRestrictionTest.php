<?php

namespace Tests\Unit;

use App\Http\Controllers\AccessControl\UserController;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserSystemAdministratorRestrictionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure Group 1 (System Administrator) and Group 2 exist
        Group::firstOrCreate(
            ['id' => 1],
            ['name' => 'System Administrator', 'status' => 1]
        );
        Group::firstOrCreate(
            ['id' => 2],
            ['name' => 'Manager', 'status' => 1]
        );
    }

    public function test_system_administrators_are_excluded_from_user_list_for_non_administrators()
    {
        $manager = new User([
            'id' => 998,
            'name' => 'Manager User',
            'username' => 'mgr_test',
            'email' => 'mgr_test@store.com',
            'group_id' => 2,
            'status' => 1,
        ]);
        $this->actingAs($manager);

        $response = app(UserController::class)->index();

        $data = $response->getData();
        $this->assertArrayHasKey('users', $data);

        foreach ($data['users'] as $u) {
            $this->assertNotEquals(1, $u->group_id, "System Administrator user (group_id = 1) must be hidden from non-administrators.");
        }
    }

    public function test_non_system_administrator_cannot_see_group_1_in_create_form()
    {
        $manager = new User([
            'id' => 998,
            'name' => 'Manager User',
            'username' => 'mgr_test',
            'email' => 'mgr_test@store.com',
            'group_id' => 2,
            'status' => 1,
        ]);
        $this->actingAs($manager);

        $controller = app(UserController::class);
        $response = $controller->create();

        $data = $response->getData();
        $this->assertArrayHasKey('groups', $data);
        $groupIds = $data['groups']->pluck('id')->toArray();

        $this->assertNotContains(1, $groupIds, "Non-admin user must not see System Administrator group in create form.");
    }

    public function test_system_administrator_can_see_group_1_in_create_form()
    {
        $admin = User::find(1);
        $this->actingAs($admin);

        $controller = app(UserController::class);
        $response = $controller->create();

        $data = $response->getData();
        $this->assertArrayHasKey('groups', $data);
        $groupIds = $data['groups']->pluck('id')->toArray();

        $this->assertContains(1, $groupIds, "System Administrator must be able to see System Administrator group in create form.");
    }

    public function test_non_system_administrator_cannot_create_system_administrator()
    {
        $manager = new User([
            'id' => 998,
            'name' => 'Manager User',
            'username' => 'mgr_test',
            'email' => 'mgr_test@store.com',
            'group_id' => 2,
            'status' => 1,
        ]);
        $this->actingAs($manager);

        $controller = app(UserController::class);
        $request = Request::create('/user', 'POST', [
            'name' => 'Hacker Admin',
            'username' => 'hacker',
            'email' => 'hacker@store.com',
            'password' => 'secret123',
            'group_id' => 1,
        ]);

        $response = $controller->store($request);
        $this->assertEquals('Only a System Administrator can create another System Administrator.', session('error'));
    }

    public function test_non_system_administrator_cannot_edit_system_administrator()
    {
        $manager = new User([
            'id' => 998,
            'name' => 'Manager User',
            'username' => 'mgr_test',
            'email' => 'mgr_test@store.com',
            'group_id' => 2,
            'status' => 1,
        ]);
        $this->actingAs($manager);

        $controller = app(UserController::class);
        $response = $controller->edit(1);

        $this->assertEquals('Only a System Administrator can edit a System Administrator.', session('error'));
    }
}
