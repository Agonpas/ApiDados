<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //creating roles
        $admin = Role::create(['name' => 'admin']);
        $player = Role::create(['name' => 'player']);
        //creating permission
        Permission::create(['name' => 'getGames'])->syncRoles([$admin, $player]);
        Permission::create(['name' => 'destroyAllGames'])->syncRoles([$admin, $player]);
        Permission::create(['name' => 'createGame'])->syncRoles([$admin, $player]);
        Permission::create(['name' => 'winPercentage'])->syncRoles([$admin, $player]);
        
        Permission::create(['name' => 'index'])->syncRoles([$admin, $player]);
        Permission::create(['name' => 'allUsersWinPercentage'])->syncRoles([$admin, $player]);
        Permission::create(['name' => 'getTotalWinPercentage'])->syncRoles([$admin, $player]);
    }
}
