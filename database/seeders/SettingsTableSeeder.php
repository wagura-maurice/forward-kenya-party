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
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'item' => 'AFRICAS_TALKING_API_KEY',
                'default_value' => 'atsk_fdbc1b84cbafe59c554ac5384f451c4dd3e5a867c86f7e9ceb576ad4646722693aaf38de',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'item' => 'AFRICAS_TALKING_SENDER_ID',
                'default_value' => NULL, // 'Sandbox',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'item' => 'AFRICAS_TALKING_MESSAGING_ENDPOINT',
                'default_value' => 'https://api.sandbox.africastalking.com/version1/messaging',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'item' => 'AFRICAS_TALKING_USER_ENDPOINT',
                'default_value' => 'https://api.sandbox.africastalking.com/version1/user',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-06-30 21:28:44',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'item' => 'AFRICAS_TALKING_BALANCE',
                'default_value' => '0',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-22 14:12:35',
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'item' => 'MPESA_LNMO_CONSUMER_KEY',
                'default_value' => 'uKxU78Y9q2cFruO2fKRWuofRCObzMQh8',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'item' => 'MPESA_LNMO_CONSUMER_SECRET',
                'default_value' => 'By9NUqT7NGhzy5Pj',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'item' => 'MPESA_LNMO_ENVIRONMENT',
                'default_value' => 'sandbox',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'item' => 'MPESA_LNMO_INITIATOR_PASSWORD',
                'default_value' => 'HaVh3tgp',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'item' => 'MPESA_LNMO_INITIATOR_USERNAME',
                'default_value' => 'testapi779',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'item' => 'MPESA_LNMO_PASS_KEY',
                'default_value' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'item' => 'MPESA_LNMO_SHORT_CODE',
                'default_value' => '174379',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-07-02 23:48:14',
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'item' => 'WAHA_API_URL',
                'default_value' => 'http://84.247.143.79:3000',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-12 00:00:00',
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'item' => 'WAHA_API_KEY',
                'default_value' => '782338157f924709910c1fbc2635faff',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-12 00:00:00',
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'item' => 'WAHA_SESSION',
                'default_value' => 'default',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-12 00:00:00',
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'item' => 'IPPMS_BASE_URL',
                'default_value' => 'https://api-ippms.orpp.or.ke',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-15 00:00:00',
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'item' => 'IPPMS_USERNAME',
                'default_value' => 'wagura465@gmail.com',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-15 00:00:00',
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'item' => 'IPPMS_PASSWORD',
                'default_value' => 'FKP@Kenya872',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-15 00:00:00',
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'item' => 'RECAPTCHA_ENABLED',
                'default_value' => 'true',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-15 00:00:00',
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'item' => 'RECAPTCHA_SITE_KEY',
                'default_value' => '6Lclb7YrAAAAAH1ZmtSFhBpYFspgpDe2rOlLmnF9',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-15 00:00:00',
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'item' => 'RECAPTCHA_SECRET_KEY',
                'default_value' => '6Lclb7YrAAAAAJpCovLB70HijtOO7oAhD_M-6RU_',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-15 00:00:00',
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'item' => 'RECAPTCHA_SKIP_IP',
                'default_value' => '',
                'current_value' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-05-15 00:00:00',
                'updated_at' => NULL,
            ),
        ));
    }
}