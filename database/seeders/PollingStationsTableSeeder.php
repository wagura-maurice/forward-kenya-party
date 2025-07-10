<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PollingStationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('polling_stations')->delete();
        
        
        
    }
}