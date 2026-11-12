<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add publication package metadata columns to the payments table.
     * These columns store which package the EO purchased so the invoice
     * can display the correct package name and duration even if the
     * package catalogue changes in the future.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Package slug used as the canonical identifier (e.g. "professional")
            $table->string('package_name')->nullable()->after('event_id');
            // Duration in days granted by this package (e.g. 7, 14, 30, 60)
            $table->unsignedSmallInteger('package_duration_days')->nullable()->after('package_name');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['package_name', 'package_duration_days']);
        });
    }
};

