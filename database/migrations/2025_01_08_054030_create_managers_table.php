<?php

use App\Models\Manager;
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
      Schema::create('managers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the manager');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
            $table->string('designation')->nullable()->comment('Specific administrative designation assigned to the manager');
            $table->json('permissions')->nullable()->comment('List of permissions or privileges assigned to the manager');
            $table->integer('_status')->default(Manager::PENDING)
                  ->comment('Status of the manager: 0 = Pending, 1 = Active, 2 = Inactive, 3 = Suspended');
            $table->softDeletes();
            $table->timestamps();
        });           
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
