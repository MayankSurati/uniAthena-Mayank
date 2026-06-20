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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')
                ->unique();

            $table->foreignId('patient_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('appointment_slot_id')
                ->constrained('appointment_slots')
                ->cascadeOnDelete();

            $table->enum('status', [
                'booked',
                'completed',
                'cancelled',
                'rescheduled',
            ])->default('booked');

            $table->text('notes')->nullable();

            $table->timestamp('cancelled_at')->nullable();

            $table->index([
                'doctor_id',
                'status'
            ]);

            $table->index([
                'patient_id',
                'status'
            ]);

            $table->index('created_at');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
