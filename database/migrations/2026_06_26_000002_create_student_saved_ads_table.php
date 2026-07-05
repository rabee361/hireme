<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_saved_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ad_id')->constrained('ads')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'ad_id'], 'student_saved_ads_pair_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_saved_ads');
    }
};
