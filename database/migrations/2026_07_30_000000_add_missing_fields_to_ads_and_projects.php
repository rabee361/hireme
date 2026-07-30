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
        Schema::table('ads', function (Blueprint $table) {
            $table->text('description')->after('job_name');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->text('additional_details')->nullable()->after('details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('additional_details');
        });
    }
};
