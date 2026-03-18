<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HikvisionRawLog extends Model
{
    protected $table = 'hikvision_raw_logs';

    protected $fillable = [
        'employee_id',
        'access_date_and_time',
        'access_date',
        'access_time',
        'authentication_result',
        'authentication_type',
        'device_name',
        'device_serial_no',
        'first_name',
        'last_name',
        'card_number',
        'direction',
        'mask_wearing_status',
        'attendance_status',
        'processed',
        'processed_at',
        'process_error',
    ];

    protected $casts = [
        'access_date_and_time' => 'datetime',
        'access_date'          => 'date',
        'processed'            => 'boolean',
        'processed_at'         => 'datetime',
    ];

    // Scope to get only unprocessed rows — used by the sync job
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    // Relationship to employee via hikvision_id
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'hikvision_id');
    }

    // Mark this row as successfully processed
    public function markProcessed(): void
    {
        $this->update([
            'processed'     => true,
            'processed_at'  => now(),
            'process_error' => null,
        ]);
    }

    // Mark this row as failed with an error message
    public function markFailed(string $error): void
    {
        $this->update([
            'processed'     => false,
            'process_error' => $error,
        ]);
    }
}