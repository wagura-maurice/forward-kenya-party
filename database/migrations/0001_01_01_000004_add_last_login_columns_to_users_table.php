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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')
                ->after('remember_token')
                ->nullable()
                ->comment('The last time the user logged in');

            $table->string('last_login_ip', 45)
                ->after('last_login_at')
                ->nullable()
                ->comment('The IP address of the last login');

            $table->string('last_login_device')
                ->after('last_login_ip')
                ->nullable()
                ->comment('The device used for the last login');

            $table->text('last_login_user_agent')
                ->after('last_login_device')
                ->nullable()
                ->comment('The user agent of the last login');

            $table->string('last_login_os')
                ->after('last_login_user_agent')
                ->nullable()
                ->comment('The operating system of the last login');

            $table->string('last_login_location')
                ->after('last_login_os')
                ->nullable()
                ->comment('The location of the last login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'last_login_ip',
                'last_login_device',
                'last_login_user_agent',
                'last_login_os',
                'last_login_location',
            ]);
        });
    }
};
