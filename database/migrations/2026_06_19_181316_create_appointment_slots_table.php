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
        Schema::create('appointment_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('availability_id')
                ->constrained('doctor_availabilities')
                ->cascadeOnDelete();

            $table->date('slot_date');

            $table->time('start_at');
            $table->time('end_at');

            $table->enum('status', [
                'available',
                'booked',
                'blocked'
            ])->default('available');

            $table->index([
                'doctor_id',
                'slot_date',
                'status'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_slots');
    }
};
