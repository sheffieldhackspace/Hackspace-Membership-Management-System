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
        Schema::create('members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name');
            $table->string('known_as');
            $table->timestamps();
        });

        Schema::create('membership_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('member_id')->index();
            $table->foreign('member_id')->references('id')->on('members');
            $table->string('membership_type_from')->nullable();
            $table->string('membership_type_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
        Schema::dropIfExists('membership_histories');
    }
};
