<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckActivityLogSetup extends Command
{
    protected $signature = 'activitylog:check-setup';
    protected $description = 'Check and fix activity log setup';

    public function handle()
    {
        $this->info('Checking activity log setup...');
        
        // Check if activities table exists
        if (!Schema::hasTable('activities')) {
            $this->error('Activities table does not exist!');
            $this->info('Running migrations...');
            
            try {
                $this->call('migrate', [
                    '--path' => 'vendor/spatie/laravel-activitylog/database/migrations/create_activity_log_table.php.stub'
                ]);
                $this->info('Activities table created successfully.');
            } catch (\Exception $e) {
                $this->error('Failed to create activities table: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('✓ Activities table exists');
        }
        
        // Check required columns
        $requiredColumns = [
            'id', 'log_name', 'description', 'subject_type', 'subject_id',
            'causer_type', 'causer_id', 'properties', 'created_at', 'updated_at'
        ];
        
        $missingColumns = [];
        $tableColumns = Schema::getColumnListing('activities');
        
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $tableColumns)) {
                $missingColumns[] = $column;
            }
        }
        
        if (!empty($missingColumns)) {
            $this->error('Missing columns in activities table: ' . implode(', ', $missingColumns));
            return 1;
        } else {
            $this->info('✓ All required columns exist in activities table');
        }
        
        // Check if activity logging is enabled in config
        $enabled = config('activitylog.enabled', true);
        $this->info('Activity logging is ' . ($enabled ? 'enabled' : 'disabled') . ' in config');
        
        if (!$enabled) {
            $this->warn('Warning: Activity logging is disabled in config. Set ACTIVITY_LOGGER_ENABLED=true in .env');
        }
        
        // Test logging an activity
        $this->info('\nTesting activity logging...');
        
        try {
            activity('test')
                ->causedBy(auth()->user())
                ->withProperties(['test' => 'value'])
                ->log('Test activity');
                
            $this->info('✓ Successfully logged a test activity');
            
            // Show the last activity
            $activity = DB::table('activities')
                ->orderBy('id', 'desc')
                ->first();
                
            $this->info('\nLast activity:');
            $this->table(
                ['ID', 'Description', 'Subject', 'Causer', 'Created At'],
                [[
                    $activity->id,
                    $activity->description,
                    $activity->subject_type . ':' . $activity->subject_id,
                    ($activity->causer_type ?? 'N/A') . ':' . ($activity->causer_id ?? 'N/A'),
                    $activity->created_at
                ]]
            );
            
        } catch (\Exception $e) {
            $this->error('Failed to log test activity: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
