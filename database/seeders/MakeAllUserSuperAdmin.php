<?php

namespace Database\Seeders;

use App\Enums\DefaultRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MakeAllUserSuperAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::findByName(DefaultRole::SUPER_ADMIN->value);

        $users = User::all();

        foreach ($users as $user) {
            /** @var User */
            $user->assignRole($role);
        }
    }
}
