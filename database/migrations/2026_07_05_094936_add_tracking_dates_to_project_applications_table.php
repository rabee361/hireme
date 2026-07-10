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
        Schema::table('project_applications', function (Blueprint $table) {
            $table->timestamp('client_approval_date')->nullable();
            $table->timestamp('delivery_deadline_date')->nullable();
            $table->timestamp('trial_ends_at_date')->nullable();
            $table->string('submission_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_applications', function (Blueprint $table) {
            $table->dropColumn(['client_approval_date', 'delivery_deadline_date', 'trial_ends_at_date', 'submission_link']);
        });
    }
};
