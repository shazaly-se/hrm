<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;
  use App\Models\User;
class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        $user= User::find(1);
        $user->givePermissionTo('list-post');
        // $permissions = [
        //    'role-list',
        //    'role-create',
        //    'role-edit',
        //    'role-delete',
        //    'product-list',
        //    'product-create',
        //    'product-edit',
        //    'product-delete'
        // ];
     
        // foreach ($permissions as $permission) {
        //      Permission::create(['name' => $permission]);
        // }
    }

}
