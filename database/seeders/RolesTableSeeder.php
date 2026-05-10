<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'uuid' => 'a0d06231-db32-4fa4-9558-c27875946f9c',
                'name' => 'administrator',
                'slug' => 'administrator',
                'description' => 'Full system administrative privileges',
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            1 => 
            array (
                'id' => 2,
                'uuid' => '4142ff38-17f6-4475-a951-f9ec00314006',
                'name' => 'manager',
                'slug' => 'manager',
                'description' => 'Secretariat-level access for managing user registrations and system oversight',
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            2 => 
            array (
                'id' => 3,
                'uuid' => 'ea44d157-55b9-4a0a-8b6a-732859b86673',
                'name' => 'member',
                'slug' => 'member',
                'description' => 'Primary role for all successfully registered users',
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            3 => 
            array (
                'id' => 4,
                'uuid' => '49f1eb4b-dd23-472b-b09d-5801b81e0343',
                'name' => 'guest',
                'slug' => 'guest',
                'description' => 'Restricted access for public or demo-only viewing',
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
        ));
        
        
    }
}