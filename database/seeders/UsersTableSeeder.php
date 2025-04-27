<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Qs;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // DB::table('users')->delete();

        $this->createNewUsers();
        //$this->createManyUsers( 3);
    }

    protected function createNewUsers()
    {
        $password = Hash::make('cpa'); // Default user password

        $d = [

            ['name' => 'RANAIVONDRAMBOLA Herimino',
                'email' => 'herimin0@cpa.com',
                'username' => 'Herimino',
                'password' => $password,
                'user_type' => 'accountant',
                'code' => strtoupper(Str::random(10)),
                'remember_token' => Str::random(10),
            ],

            
        ];
        DB::table('users')->insert($d);
    }

  
}
