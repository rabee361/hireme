<?php

use App\Enums\UserType;
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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'username');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('type')->default(UserType::Customer->value);
            $table->string('phone_number')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('avatar')->nullable();
            $table->unique('username');
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('address');
            $table->decimal('hour_cost', 8, 2)->unsigned();
            $table->unsignedSmallInteger('experience_years');
            $table->string('tech1')->nullable();
            $table->string('tech2')->nullable();
            $table->string('tech3')->nullable();
            $table->string('college');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('address');
            $table->decimal('hour_cost', 8, 2)->unsigned();
            $table->unsignedSmallInteger('experience_years');
            $table->string('tech1')->nullable();
            $table->string('tech2')->nullable();
            $table->string('tech3')->nullable();
            $table->string('college');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->date('started_at');
            $table->unsignedInteger('employees_count');
            $table->string('tech1')->nullable();
            $table->string('tech2')->nullable();
            $table->string('tech3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
        Schema::dropIfExists('customer_profiles');
        Schema::dropIfExists('student_profiles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn([
                'type',
                'phone_number',
                'description',
                'cover_image',
                'avatar',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
        });
    }
};