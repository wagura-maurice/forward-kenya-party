<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the applications core database tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // logic for looping database tables though Orange hill's i-seed commands.
            Artisan::call('iseed settings,roles,abilities,countries,regions,counties,sub_counties,constituencies,wards,locations,villages,refugee_centers,consulates,polling_stations,consulates,refugee_centers,banks --force');

            // Artisan::call('iseed media_types,media_categories,document_types,document_categories,department_types,department_categories,service_types,service_categories,activity_types,activity_categories,notification_types,notification_categories,currency_types,currency_categories,account_types,account_categories,receipt_types,receipt_categories,invoice_types,invoice_categories,transaction_types,transaction_categories,feedback_types,feedback_categories,ticket_types,ticket_categories,announcement_types,announcement_categories,communication_types,communication_categories --force');

            // Artisan::call('iseed departments,services --force');
            
            return Command::SUCCESS;
        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}
