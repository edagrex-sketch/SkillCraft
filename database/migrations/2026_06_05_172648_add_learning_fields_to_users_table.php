<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('learning_style')->nullable();
            $table->integer('time_availability_minutes')->nullable();
            $table->text('goals')->nullable();
            $table->string('experience_level')->default('beginner');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['learning_style', 'time_availability_minutes', 'goals', 'experience_level']);
        });
    }
};
