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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('user_type');
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->string('req1');
            $table->string('req2')->nullable();
            $table->string('req3')->nullable();
            $table->string('req4')->nullable();
            $table->string('req5')->nullable();
            $table->string('task1');
            $table->string('task2')->nullable();
            $table->string('task3')->nullable();
            $table->string('task4')->nullable();
            $table->string('task5')->nullable();
            $table->string('feature1');
            $table->string('feature2')->nullable();
            $table->string('feature3')->nullable();
            $table->string('feature4')->nullable();
            $table->string('feature5')->nullable();
            $table->text('additional_details')->nullable();
            $table->boolean('github_required');
            $table->boolean('resume_required');
            $table->boolean('prev_work_required');
            $table->boolean('expected_salary_required');
            $table->foreignId('company_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->text('details')->nullable();
            $table->string('tool1');
            $table->string('tool2');
            $table->string('tool3');
            $table->string('tool4');
            $table->string('tool5');
            $table->string('name');
            $table->string('cover_image');
            $table->timestamps();
        });

        Schema::create('ad_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->string('github_link');
            $table->unsignedInteger('expected_salary')->nullable();
            $table->string('resume');
            $table->foreignId('ad_id')->constrained('ads')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('project_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->string('github_link');
            $table->unsignedInteger('expected_salary')->nullable();
            $table->string('resume');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_applications');
        Schema::dropIfExists('ad_applications');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('ads');
        Schema::dropIfExists('notifications');
    }
};