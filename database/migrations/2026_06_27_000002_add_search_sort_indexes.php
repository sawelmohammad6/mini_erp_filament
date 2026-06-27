<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('name');
            $table->index('email');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['email']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
