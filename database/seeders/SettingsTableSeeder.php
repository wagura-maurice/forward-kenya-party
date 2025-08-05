<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'item' => 'AFRICAS_TALKING_USERNAME',
                'default_value' => 'sandbox',
                'current_value' => 'e-granaryKe',
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'item' => 'AFRICAS_TALKING_API_KEY',
                'default_value' => 'atsk_005585bbb27958199e5b6ce45bad2edb2502ffdc7149e436f9df85898216cffa704f9002',
                'current_value' => 'atsk_b3586ada36a858740769698f17fbc4c4814723d8ceaf52c3bd26abc2abc64057ff30b7a4',
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'item' => 'AFRICAS_TALKING_SENDER_ID',
                'default_value' => 'Sandbox',
                'current_value' => 'eGRANARYKe',
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'item' => 'AFRICAS_TALKING_MESSAGING_ENDPOINT',
                'default_value' => 'https://api.sandbox.africastalking.com/version1/messaging',
                'current_value' => 'https://api.africastalking.com/version1/messaging',
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'item' => 'AFRICAS_TALKING_USER_ENDPOINT',
                'default_value' => 'https://api.sandbox.africastalking.com/version1/user',
                'current_value' => 'https://api.africastalking.com/version1/user',
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'item' => 'MPESA_LNMO_CONSUMER_KEY',
                'default_value' => 'uKxU78Y9q2cFruO2fKRWuofRCObzMQh8',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'item' => 'MPESA_LNMO_CONSUMER_SECRET',
                'default_value' => 'By9NUqT7NGhzy5Pj',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'item' => 'MPESA_LNMO_ENVIRONMENT',
                'default_value' => 'sandbox',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'item' => 'MPESA_LNMO_INITIATOR_PASSWORD',
                'default_value' => 'HaVh3tgp',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'item' => 'MPESA_LNMO_INITIATOR_USERNAME',
                'default_value' => 'testapi779',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'item' => 'MPESA_LNMO_PASS_KEY',
                'default_value' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'item' => 'MPESA_LNMO_SHORT_CODE',
                'default_value' => '174379',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'item' => 'AFRICAS_TALKING_BALANCE',
                'default_value' => '0',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-22 14:12:35',
                'updated_at' => NULL,
            ),
        ));
    }
}