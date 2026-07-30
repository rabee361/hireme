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
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->string('git_link')->nullable();
            $table->string('linked_link')->nullable();
            $table->text('bio')->nullable();
            $table->string('university_name')->nullable();
            $table->boolean('is_graduated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
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
