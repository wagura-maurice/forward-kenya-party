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
                'description' => NULL,
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
                'description' => NULL,
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            2 => 
            array (
                'id' => 3,
                'uuid' => 'ea44d157-55b9-4a0a-8b6a-732859b86673',
                'name' => 'citizen',
                'slug' => 'citizen',
                'description' => NULL,
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            3 => 
            array (
                'id' => 4,
                'uuid' => '06af6bcc-2005-4db6-aeb8-5e3c7da75ad1',
                'name' => 'resident',
                'slug' => 'resident',
                'description' => NULL,
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            4 => 
            array (
                'id' => 5,
                'uuid' => '385d9a3f-be6c-4c79-b17a-a2d00b51f978',
                'name' => 'refugee',
                'slug' => 'refugee',
                'description' => NULL,
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            5 => 
            array (
                'id' => 6,
                'uuid' => 'e621fafa-98b3-4fc8-819f-487799cbff79',
                'name' => 'diplomat',
                'slug' => 'diplomat',
                'description' => NULL,
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            6 => 
            array (
                'id' => 7,
                'uuid' => '1802a89c-a334-40a4-b28c-80a445cbcc64',
                'name' => 'foreigner',
                'slug' => 'foreigner',
                'description' => NULL,
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
            7 => 
            array (
                'id' => 8,
                'uuid' => '49f1eb4b-dd23-472b-b09d-5801b81e0343',
                'name' => 'guest',
                'slug' => 'guest',
                'description' => NULL,
                'configuration' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-12-16 18:22:14',
                'updated_at' => '2024-12-16 18:22:14',
            ),
        ));
        
        
    }
}