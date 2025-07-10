<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DepartmentServiceTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('department_service')->delete();
        
        \DB::table('department_service')->insert(array (
            0 => 
            array (
                'service_id' => 1,
                'department_id' => 3,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:02:54',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'service_id' => 2,
                'department_id' => 1,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:03:20',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'service_id' => 3,
                'department_id' => 2,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:03:54',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'service_id' => 4,
                'department_id' => 5,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:05:29',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'service_id' => 5,
                'department_id' => 7,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:05:50',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'service_id' => 6,
                'department_id' => 8,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:06:18',
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'service_id' => 7,
                'department_id' => 9,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:07:29',
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'service_id' => 8,
                'department_id' => 10,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:08:01',
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'service_id' => 9,
                'department_id' => 11,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:08:25',
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'service_id' => 10,
                'department_id' => 12,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => '2025-06-22 21:08:46',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}