<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
               'name'=>'naveed',
               'email'=>'naveed.ahmad@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user2',
               'email'=>'user2@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user3',
               'email'=>'user3@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user4',
               'email'=>'user4@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user5',
               'email'=>'user5@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user6',
               'email'=>'user6@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user7',
               'email'=>'user7@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user8',
               'email'=>'user8@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user9',
               'email'=>'user9@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'user10',
               'email'=>'user10@ikonic.com',
               'password'=> bcrypt('123456'),
            ],
        ];
  
        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
