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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('git_link')->nullable();
            $table->string('linked_link')->nullable();
            $table->text('bio')->nullable();
            $table->string('university_name')->nullable();
            $table->boolean('is_graduated')->default(false);
        });

        Schema::create('student_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->string('exp_name');
            $table->boolean('is_freelancer')->default(false);
            $table->string('job_title');
            $table->string('start_date');
            $table->string('end_date')->nullable();
            $table->boolean('until_now')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_experiences');

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'git_link',
                'linked_link',
                'bio',
                'university_name',
                'is_graduated',
            ]);
        });
    }
};
