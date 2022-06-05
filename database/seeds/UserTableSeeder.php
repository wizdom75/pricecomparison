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
        if ($user = User::find(1)) {
            $user->update([
                'name'      => 'admin',
                'email'     => 'admin@lowprices4u.co.uk',
                'password'  => Hash::make('password'),
            ]);
        } else {
            User::insertIgnore([
                'name'      => 'admin',
                'email'     => 'admin@lowprices4u.co.uk',
                'password'  => Hash::make('password'),
            ]);
        }

    }
}
