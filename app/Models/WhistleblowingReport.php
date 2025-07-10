<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WhistleblowingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_type',
        'description',
        'individuals_involved',
        'evidence_details',
        'evidence_file_path',
        'reported_before',
        'reported_details',
        'suggested_resolution',
        'ip_address',
        'user_agent',
        'tracking_id'
    ];

    /**
     * Get the public URL for the evidence file
     */
    public function getEvidenceFileUrl()
    {
        return $this->evidence_file_path 
            ? Storage::url($this->evidence_file_path)
            : null;
    }
}