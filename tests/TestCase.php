<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure we're using the MySQL database for testing
        if (config('database.default') !== 'mysql') {
            config(['database.default' => 'mysql']);
        }
        
        // Enable activity logging for tests
        config(['activitylog.enabled' => true]);
    }
}
