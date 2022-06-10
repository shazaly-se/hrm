<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // permissions
        $addUser= 'add user';
        $editUser= 'edit user';
        $deleteUser= 'delete user';
        $listUser= 'list user';

        $addPost= 'add post';
        $editPost= 'edit post';
        $deletePost= 'delete post';
        $listPost= 'list post';

        Permission::create(['name' => $addUser]);
        Permission::create(['name' => $editUser]);
        Permission::create(['name' => $deleteUser]);
        Permission::create(['name' => $listUser]);
        Permission::create(['name' => $addPost]);
        Permission::create(['name' => $editPost]);
        Permission::create(['name' => $deletePost]);
        Permission::create(['name' => $listPost]);

        // roles
        $superAdmin= 'super-admin';
        $systemAdmin= 'system-admin';
        $customer= 'customer';

        Role::create(['name' => $superAdmin])->givePermissionTo(Permission::all());
        Role::create(['name' => $systemAdmin])->givePermissionTo([
            $addPost,
            $editPost,
            $deletePost
        ]);
        Role::create(['name' => $customer])->givePermissionTo([
            $listPost
          
        ]);
    }
}
