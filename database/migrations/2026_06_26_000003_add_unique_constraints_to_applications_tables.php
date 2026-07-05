<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_applications', function (Blueprint $table) {
            $table->unique(['student_profile_id', 'ad_id'], 'ad_applications_student_ad_unique');
        });

        Schema::table('project_applications', function (Blueprint $table) {
            $table->unique(['student_profile_id', 'project_id'], 'project_applications_student_project_unique');
        });
    }

    public function down(): void
    {
        Schema::table('ad_applications', function (Blueprint $table) {
            $table->dropUnique('ad_applications_student_ad_unique');
        });

        Schema::table('project_applications', function (Blueprint $table) {
            $table->dropUnique('project_applications_student_project_unique');
        });
    }
};
