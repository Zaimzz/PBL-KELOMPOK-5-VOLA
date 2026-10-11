<?php

use App\Enums\ApplicationStatus;
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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('event_positions')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending')->index();
            $table->text('cover_letter')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestamps();

            $table->unique(['volunteer_profile_id', 'position_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
