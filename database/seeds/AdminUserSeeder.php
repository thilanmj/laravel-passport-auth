<?php

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'Thilan',
            'last_name' => 'Madusanka',
            'email' => 'thilan87189@gmail.com',
            'password' => bcrypt('12345'),
            'address' => '',
            'city' => '',
            'mobile' => '',
            'verified'=>0,
            'verify_token'=>'',
        ]);
    }
}
