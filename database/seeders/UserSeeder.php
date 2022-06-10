<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       User::factory()->count(1)
        ->create()
         ->each(
             function($user){
           $user->assignRole('super-admin');
       });
       User::factory()->count(2)
       ->create()
        ->each(
            function($user){
          $user->assignRole('system-admin');
      });
      User::factory()->count(3)
      ->create()
       ->each(
           function($user){
         $user->assignRole('customer');
     });
    }
}
