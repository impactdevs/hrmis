<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Evidence extends Model
{
    protected $table = 'evidences';

    protected $primaryKey = 'evidence_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'witness_name',
        'document',
        'email',
        'whistleblower_id'
    ];

    protected static function booted()
    {
        static::creating(function ($evidence) {
            $evidence->evidence_id = $evidence->evidence_id ?? (string) Str::uuid();
        });
    }
    public function whistleblower(): BelongsTo
    {
        return $this->belongsTo(Whistleblower::class, 'whistleblower_id');
    }
}
