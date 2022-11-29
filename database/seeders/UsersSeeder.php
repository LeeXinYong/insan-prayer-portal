<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserInfo;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        // User::query()->truncate();

        $userData = [[
            "name" => "Afif Zafri",
            "email" => "afif@redant.my",
            "password" => bcrypt("abc123"),
            "created_at" => now(),
            "updated_at" => now(),
        ],[
            "name" => "Chan Yung Keat",
            "email" => "chan@redant.my",
            "password" => bcrypt("abc123"),
            "created_at" => now(),
            "updated_at" => now(),
        ],[
            "name" => "Mah Hou Lok",
            "email" => "lok@redant.my",
            "password" => bcrypt("abc123"),
            "created_at" => now(),
            "updated_at" => now(),
        ],[
            "name" => "Lee Shu Jiun",
            "email" => "shujiun@redant.my",
            "password" => bcrypt("abc123"),
            "created_at" => now(),
            "updated_at" => now(),
        ],[
            "name" => "Lee Xin Yong",
            "email" => "xinyong@redant.my",
            "password" => bcrypt("abc123"),
            "created_at" => now(),
            "updated_at" => now(),
        ]];

        foreach($userData as $userDatum) {
            /** @var User $user */
            $user = User::query()->create($userDatum);
            $this->addDummyInfo($faker, $user);
        }
    }

    private function addDummyInfo(Generator $faker, User $user)
    {
        $dummyInfo = [
            "company" => $faker->company,
            "phone" => $faker->phoneNumber,
            "website" => $faker->url,
            "language" => $faker->languageCode,
            "country" => $faker->countryCode,
        ];

        $info = new UserInfo();
        foreach ($dummyInfo as $key => $value) {
            $info->$key = $value;
        }
        $info->user()->associate($user);
        $info->save();
    }
}
