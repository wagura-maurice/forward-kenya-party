<?php

use App\Models\Administrator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the administrator');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
            $table->string('designation')->nullable()->comment('Specific administrative designation assigned to the administrator');
            $table->json('permissions')->nullable()->comment('List of permissions or privileges assigned to the administrator');
            $table->integer('_status')->default(Administrator::PENDING)
                  ->comment('Status of the administrator: 0 = Pending, 1 = Active, 2 = Inactive, 3 = Suspended');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrators');
    }
};
