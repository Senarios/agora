<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class admin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//         User::create([
//             'name' => 'Admin',
//             'email' => 'superadmin@gmail.com',
            
//             'password' => bcrypt('12345678'),
// //            'confirmation_code' => md5(uniqid(mt_rand(), true)),
// //            'confirmed' => true,
//         ]);
         User::create([
            'name' => 'Admin2',
            'email' => 'superadmin2@gmail.com',
            
            'password' => bcrypt('12345678'),
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
        ]);
    }
}
