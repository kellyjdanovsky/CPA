<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('class_types')->delete();

        $data = [
            ['name' => 'Prescolaire', 'code' => 'PR'],
            ['name' => 'Primaire', 'code' => 'PM'],
            ['name' => 'Secondaire', 'code' => 'SC'],
        ];

        DB::table('class_types')->insert($data);

    }
}
