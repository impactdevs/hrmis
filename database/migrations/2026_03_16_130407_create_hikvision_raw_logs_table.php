<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hikvision_raw_logs', function (Blueprint $table) {
            $table->id();                                      // simple auto-increment the device can write to
            $table->string('employee_id');                     // device's numeric ID — maps to employees.hikvision_id
            $table->datetime('access_date_and_time')->nullable();
            $table->date('access_date')->nullable();
            $table->time('access_time')->nullable();
            $table->string('authentication_result')->nullable(); // 'Succeeded' | 'Failed'
            $table->string('device_name')->nullable();
            $table->string('device_serial_no')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('card_number')->nullable();
            $table->string('direction')->nullable();            // 'Enter' | 'Exit'
            $table->string('attendance_status')->nullable();    // 'Check-In' | 'Check-Out' | 'Break Out' etc.
            $table->boolean('processed')->default(false);       // flipped to true once Laravel has handled this row
            $table->timestamp('processed_at')->nullable();      // when it was processed
            $table->text('process_error')->nullable();          // error message if processing failed
            $table->timestamps();                               // created_at = when device pushed the record

            // Index for the processing job — only ever queries unprocessed rows
            $table->index(['processed', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hikvision_raw_logs');
    }
};