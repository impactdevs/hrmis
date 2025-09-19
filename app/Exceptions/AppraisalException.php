<?php

namespace App\Exceptions;

use Exception;

class AppraisalException extends Exception
{
    /**
     * User-friendly error messages for different appraisal issues
     */
    protected static array $friendlyMessages = [
        'no_employee_record' => 'Your employee record was not found. Please contact Human Resources for assistance.',
        'no_department_assigned' => 'You are not assigned to a department. Please contact HR to assign you to a department.',
        'no_department_head' => 'Your department does not have a department head assigned. Please contact the administrator.',
        'invalid_department_head_role' => 'Your department head does not have the required permissions to approve appraisals. Please contact HR.',
        'no_executive_secretary' => 'No Executive Secretary is available to process your appraisal. Please contact the administrator.',
        'unauthorized_role' => 'You do not have permission to perform this action. Contact your administrator if you believe this is an error.',
        'appraisal_already_reviewed' => 'This appraisal has already been reviewed and cannot be edited at this time.',
        'appraisee_cannot_edit_after_supervisor_review' => 'You cannot edit this appraisal because your supervisor has already reviewed it. If you need to make changes, please contact your supervisor.',
        'appraisee_cannot_edit_after_hr_review' => 'You cannot edit this appraisal because HR has already reviewed it. For any changes, please contact Human Resources.',
        'appraisee_cannot_edit_after_es_review' => 'You cannot edit this appraisal because the Executive Secretary has already reviewed it. The appraisal process is now complete.',
        'appraiser_cannot_access_attachments' => 'As an appraiser, you do not have permission to view or download attachments in this appraisal. This restriction helps maintain the objectivity of the appraisal process.',
        'invalid_stage_transition' => 'Cannot advance to the requested stage. The appraisal is not ready for this transition.',
        'withdrawal_not_allowed' => 'This appraisal cannot be withdrawn because it has already been processed by an approver.',
        'deadline_expired' => 'The deadline for this stage has passed. Please contact your supervisor or HR.',
        'missing_required_data' => 'Some required information is missing from the appraisal form. Please complete all mandatory fields.',
        'file_upload_error' => 'There was an issue uploading your file. Please ensure it meets the size and format requirements.',
        'notification_failed' => 'The appraisal was processed successfully, but we couldn\'t send notification emails. The relevant parties have been notified through the system.',
        'database_error' => 'A technical issue occurred while processing your request. Please try again, or contact support if the problem persists.',
    ];

    protected string $errorKey;
    protected array $context;

    public function __construct(string $errorKey, array $context = [], int $code = 400, Exception $previous = null)
    {
        $this->errorKey = $errorKey;
        $this->context = $context;
        
        $message = $this->getFriendlyMessage($errorKey, $context);
        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get user-friendly error message
     */
    private function getFriendlyMessage(string $errorKey, array $context = []): string
    {
        $baseMessage = self::$friendlyMessages[$errorKey] ?? 'An unexpected error occurred. Please try again or contact support.';
        
        // Replace placeholders in the message with context values
        foreach ($context as $key => $value) {
            $baseMessage = str_replace("{{$key}}", $value, $baseMessage);
        }
        
        return $baseMessage;
    }

    /**
     * Get the error key
     */
    public function getErrorKey(): string
    {
        return $this->errorKey;
    }

    /**
     * Get the context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Convert exception to array for API responses
     */
    public function toArray(): array
    {
        return [
            'error' => true,
            'error_key' => $this->errorKey,
            'message' => $this->getMessage(),
            'context' => $this->context,
            'code' => $this->getCode(),
        ];
    }

    /**
     * Static factory methods for common appraisal errors
     */
    public static function noEmployeeRecord(): self
    {
        return new self('no_employee_record', [], 404);
    }

    public static function noDepartmentAssigned(): self
    {
        return new self('no_department_assigned', [], 400);
    }

    public static function noDepartmentHead(string $departmentName = null): self
    {
        $context = $departmentName ? ['department' => $departmentName] : [];
        return new self('no_department_head', $context, 400);
    }

    public static function invalidDepartmentHeadRole(string $currentRole = null): self
    {
        $context = $currentRole ? ['current_role' => $currentRole] : [];
        return new self('invalid_department_head_role', $context, 400);
    }

    public static function noExecutiveSecretary(): self
    {
        return new self('no_executive_secretary', [], 500);
    }

    public static function unauthorizedRole(string $requiredRole = null): self
    {
        $context = $requiredRole ? ['required_role' => $requiredRole] : [];
        return new self('unauthorized_role', $context, 403);
    }

    public static function alreadyReviewed(): self
    {
        return new self('appraisal_already_reviewed', [], 422);
    }

    public static function appraiseeCannotEditAfterSupervisorReview(): self
    {
        return new self('appraisee_cannot_edit_after_supervisor_review', [], 422);
    }

    public static function appraiseeCannotEditAfterHRReview(): self
    {
        return new self('appraisee_cannot_edit_after_hr_review', [], 422);
    }

    public static function appraiseeCannotEditAfterESReview(): self
    {
        return new self('appraisee_cannot_edit_after_es_review', [], 422);
    }

    public static function appraiserCannotAccessAttachments(): self
    {
        return new self('appraiser_cannot_access_attachments', [], 403);
    }

    public static function invalidStageTransition(string $from = null, string $to = null): self
    {
        $context = [];
        if ($from) $context['from_stage'] = $from;
        if ($to) $context['to_stage'] = $to;
        
        return new self('invalid_stage_transition', $context, 422);
    }

    public static function withdrawalNotAllowed(): self
    {
        return new self('withdrawal_not_allowed', [], 422);
    }

    public static function deadlineExpired(string $stage = null): self
    {
        $context = $stage ? ['stage' => $stage] : [];
        return new self('deadline_expired', $context, 422);
    }

    public static function missingRequiredData(array $missingFields = []): self
    {
        return new self('missing_required_data', ['fields' => implode(', ', $missingFields)], 422);
    }

    public static function fileUploadError(string $fileName = null, string $reason = null): self
    {
        $context = [];
        if ($fileName) $context['file_name'] = $fileName;
        if ($reason) $context['reason'] = $reason;
        
        return new self('file_upload_error', $context, 422);
    }

    public static function notificationFailed(): self
    {
        return new self('notification_failed', [], 200); // Still success, just warning
    }

    public static function databaseError(string $operation = null): self
    {
        $context = $operation ? ['operation' => $operation] : [];
        return new self('database_error', $context, 500);
    }
}
