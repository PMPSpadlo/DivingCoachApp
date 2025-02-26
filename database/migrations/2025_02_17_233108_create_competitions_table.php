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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->date('date');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->string('judge_panel')->nullable();
            $table->enum('type', ['platform', 'springboard', 'synchro'])->default('springboard');
            $table->enum('level', ['regional', 'national', 'international'])->default('regional');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
