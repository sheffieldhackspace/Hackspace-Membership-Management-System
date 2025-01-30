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
        Schema::table('email_addresses', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('postal_addresses', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('members', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_addresses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('postal_addresses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('members', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
