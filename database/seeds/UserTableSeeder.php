<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insertOrIgnore([
            'name'      => 'admin',
            'email'     => 'admin@lowprices4u.co.uk',
            'password'  => Hash::make('password'),
        ]);
    }
}
