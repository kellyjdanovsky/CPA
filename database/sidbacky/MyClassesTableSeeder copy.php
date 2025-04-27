<?php
namespace Database\Seeders;

use App\Models\ClassType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MyClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('my_classes')->delete();
        $ct = ClassType::pluck('id')->all();

        $data = [
            ['name' => 'Jardin d enfant', 'class_type_id' => $ct[1]],
            ['name' => 'primaire', 'class_type_id' => $ct[1]],
            ['name' => '11 eme', 'class_type_id' => $ct[1]],
            ['name' => '12 eme', 'class_type_id' => $ct[1]],
            ['name' => '10 eme ', 'class_type_id' => $ct[1]],
            ['name' => '9 eme', 'class_type_id' => $ct[1]],
            ['name' => '8 eme', 'class_type_id' => $ct[1]],
            ['name' => '7 eme', 'class_type_id' => $ct[1]],
            ['name' => '6 eme', 'class_type_id' => $ct[1]],
            ['name' => '5 eme', 'class_type_id' => $ct[1]],
            ['name' => '4 eme', 'class_type_id' => $ct[1]],
            ['name' => '3 eme', 'class_type_id' => $ct[1]],
            ];

        DB::table('my_classes')->insert($data);

    }
}
