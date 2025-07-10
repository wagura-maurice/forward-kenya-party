<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Optimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimizing the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // logic for running laravel artisan commands.
            Artisan::call('down');
            Artisan::call('cache:clear');
            Artisan::call('auth:clear-resets');
            Artisan::call('route:clear');
            Artisan::call('route:cache');
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            Artisan::call('view:clear');
            Artisan::call('view:cache');
            Artisan::call('storage:link');
            Artisan::call('optimize');
            // Artisan::call('up');
            // logic for running composer's commands.
            shell_exec('composer dump-autoload');
            // shell_exec('composer update');

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}
