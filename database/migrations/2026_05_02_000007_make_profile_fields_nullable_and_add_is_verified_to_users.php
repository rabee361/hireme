<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('email_verified_at');
        });

        DB::table('users')
            ->whereNotNull('email_verified_at')
            ->update(['is_verified' => true]);

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->text('address')->nullable()->change();
            $table->decimal('hour_cost', 8, 2)->unsigned()->nullable()->change();
            $table->unsignedSmallInteger('experience_years')->nullable()->change();
            $table->string('college')->nullable()->change();
            $table->string('title')->nullable()->change();
        });

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->text('address')->nullable()->change();
            $table->decimal('hour_cost', 8, 2)->unsigned()->nullable()->change();
            $table->unsignedSmallInteger('experience_years')->nullable()->change();
            $table->string('college')->nullable()->change();
            $table->string('title')->nullable()->change();
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->date('started_at')->nullable()->change();
            $table->unsignedInteger('employees_count')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->date('started_at')->nullable(false)->change();
            $table->unsignedInteger('employees_count')->nullable(false)->change();
        });

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->text('address')->nullable(false)->change();
            $table->decimal('hour_cost', 8, 2)->unsigned()->nullable(false)->change();
            $table->unsignedSmallInteger('experience_years')->nullable(false)->change();
            $table->string('college')->nullable(false)->change();
            $table->string('title')->nullable(false)->change();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->text('address')->nullable(false)->change();
            $table->decimal('hour_cost', 8, 2)->unsigned()->nullable(false)->change();
            $table->unsignedSmallInteger('experience_years')->nullable(false)->change();
            $table->string('college')->nullable(false)->change();
            $table->string('title')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
};