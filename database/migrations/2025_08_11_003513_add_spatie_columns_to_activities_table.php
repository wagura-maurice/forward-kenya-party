<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add Spatie-compatible columns
        Schema::table('activities', function (Blueprint $table) {
            // Add log_name column if it doesn't exist
            if (!Schema::hasColumn('activities', 'log_name')) {
                $table->string('log_name', 255)
                      ->nullable()
                      ->after('id')
                      ->index()
                      ->comment('The log name for Spatie Activity Log');
            }
            
            // Add properties column if it doesn't exist
            if (!Schema::hasColumn('activities', 'properties')) {
                $table->json('properties')
                      ->nullable()
                      ->after('log_name')
                      ->comment('The properties for Spatie Activity Log');
            }
            
            // Add batch_uuid column if it doesn't exist
            if (!Schema::hasColumn('activities', 'batch_uuid')) {
                $table->uuid('batch_uuid')
                      ->nullable()
                      ->after('properties')
                      ->comment('The batch UUID for Spatie Activity Log');
            }
            
            // Add causer_id column if it doesn't exist
            if (!Schema::hasColumn('activities', 'causer_id')) {
                $table->foreignId('causer_id')
                      ->nullable()
                      ->after('batch_uuid')
                      ->comment('The ID of the user who caused the activity');
            }
            
            // Add causer_type column if it doesn't exist
            if (!Schema::hasColumn('activities', 'causer_type')) {
                $table->string('causer_type')
                      ->nullable()
                      ->after('causer_id')
                      ->comment('The type of the user who caused the activity');
            }
            
            // Add subject_id and subject_type columns if they don't exist
            if (!Schema::hasColumn('activities', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('causer_type');
            }
            
            if (!Schema::hasColumn('activities', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('subject_id');
            }
            
            // Add index for performance if it doesn't exist
            if (!Schema::hasIndex('activities', ['log_name', 'causer_id', 'causer_type'])) {
                $table->index(['log_name', 'causer_id', 'causer_type']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the added columns
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['log_name', 'causer_id', 'causer_type']);
            $table->dropMorphs('subject');
            $table->dropColumn([
                'log_name',
                'properties',
                'batch_uuid',
                'causer_id',
                'causer_type',
            ]);
        });
    }
};
