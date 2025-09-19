<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalHistory extends Model
{
    use HasFactory;

    protected $table = 'appraisal_history';

    protected $fillable = [
        'appraisal_id',
        'actor_id',
        'action',
        'stage_from',
        'stage_to',
        'actor_role',
        'comments',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define action constants
     */
    const ACTION_CREATED = 'created';
    const ACTION_SUBMITTED = 'submitted';
    const ACTION_APPROVED = 'approved';
    const ACTION_REJECTED = 'rejected';
    const ACTION_WITHDRAWN = 'withdrawn';
    const ACTION_EDITED = 'edited';
    const ACTION_STAGE_ADVANCED = 'stage_advanced';
    const ACTION_RESUBMITTED = 'resubmitted';
    const ACTION_RESET_TO_DRAFT = 'reset_to_draft';

    /**
     * Relationship to appraisal
     */
    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class, 'appraisal_id', 'appraisal_id');
    }

    /**
     * Relationship to the actor (employee who performed the action)
     */
    public function actor()
    {
        return $this->belongsTo(Employee::class, 'actor_id', 'employee_id')->withoutGlobalScopes();
    }

    /**
     * Create a history entry for appraisal actions
     */
    public static function logAction(
        string $appraisalId,
        string $action,
        ?string $stageFrom = null,
        ?string $stageTo = null,
        ?string $comments = null,
        ?array $metadata = null,
        ?string $actorId = null,
        ?string $actorRole = null
    ): self {
        $user = auth()->user();

        if (!$actorId && $user && $user->employee) {
            $actorId = $user->employee->employee_id;
        }

        if (!$actorRole && $user) {
            $actorRole = $user->getRoleNames()->first();
        }

        return self::create([
            'appraisal_id' => $appraisalId,
            'actor_id' => $actorId,
            'action' => $action,
            'stage_from' => $stageFrom,
            'stage_to' => $stageTo,
            'actor_role' => $actorRole,
            'comments' => $comments,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get formatted action description
     */
    public function getActionDescriptionAttribute(): string
    {
        $actorName = $this->actor ? "{$this->actor->first_name} {$this->actor->last_name}" : 'System';

        switch ($this->action) {
            case self::ACTION_CREATED:
                return "{$actorName} created the appraisal";
            case self::ACTION_SUBMITTED:
                return "{$actorName} submitted the appraisal for review";
            case self::ACTION_APPROVED:
                return "{$actorName} ({$this->actor_role}) approved the appraisal";
            case self::ACTION_REJECTED:
                $reason = $this->comments ? " - Reason: {$this->comments}" : '';
                return "{$actorName} ({$this->actor_role}) rejected the appraisal{$reason}";
            case self::ACTION_WITHDRAWN:
                return "{$actorName} withdrew the appraisal";
            case self::ACTION_EDITED:
                return "{$actorName} made changes to the appraisal";
            case self::ACTION_STAGE_ADVANCED:
                return "Appraisal advanced from {$this->stage_from} to {$this->stage_to}";
            case self::ACTION_RESUBMITTED:
                return "{$actorName} resubmitted the appraisal after addressing feedback";
            default:
                return "{$actorName} performed {$this->action} on the appraisal";
        }
    }

    /**
     * Get timeline data for display
     */
    public function getTimelineDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'description' => $this->action_description,
            'actor' => $this->actor ? [
                'name' => "{$this->actor->first_name} {$this->actor->last_name}",
                'role' => $this->actor_role,
                'staff_id' => $this->actor->staff_id,
            ] : null,
            'stage_transition' => [
                'from' => $this->stage_from,
                'to' => $this->stage_to,
            ],
            'timestamp' => $this->created_at->format('M d, Y \a\t H:i'),
            'comments' => $this->comments,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Scope to get history for a specific appraisal
     */
    public function scopeForAppraisal($query, string $appraisalId)
    {
        return $query->where('appraisal_id', $appraisalId)->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get history by action type
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
