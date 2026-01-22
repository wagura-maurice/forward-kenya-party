<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure activities table exists
        if (!Schema::hasTable('activities')) {
            $this->markTestSkipped('Activities table does not exist');
        }
    }

    /** @test */
    public function it_logs_activity_when_user_is_created()
    {
        // Enable activity logging if it's disabled in config
        config(['activitylog.enabled' => true]);
        
        // Clear any existing activities
        DB::table('activities')->truncate();
        
        // Check initial count
        $initialCount = DB::table('activities')->count();
        
        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Check if activity was logged
        $newCount = DB::table('activities')->count();
        
        $this->assertGreaterThan($initialCount, $newCount, 'No new activities were logged');
        
        // Get the latest activity
        $activity = DB::table('activities')
            ->orderBy('id', 'desc')
            ->first();
            
        $this->assertNotNull($activity, 'No activity was found');
        $this->assertStringContainsString('created', $activity->description);
        $this->assertEquals('App\\Models\\User', $activity->subject_type);
        $this->assertEquals($user->id, $activity->subject_id);
    }
}
