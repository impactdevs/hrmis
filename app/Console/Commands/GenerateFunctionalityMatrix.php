<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Regenerates public/assets/documents/uncst-matrix.pdf (the "Requirements
 * Matrix" page) from resources/views/documents/uncst-matrix-pdf.blade.php.
 *
 * The matrix used to be a hand-maintained static PDF that drifted out of
 * sync with the app. Keeping its content here means it can be regenerated
 * after future feature changes instead of hand-edited in a PDF editor.
 */
class GenerateFunctionalityMatrix extends Command
{
    protected $signature = 'matrix:generate';

    protected $description = 'Regenerate the HRMIS Functionality Matrix PDF from its Blade template';

    public function handle(): int
    {
        $html = view('documents.uncst-matrix-pdf', ['modules' => $this->modules()])->render();

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => false]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $path = public_path('assets/documents/uncst-matrix.pdf');
        file_put_contents($path, $dompdf->output());

        $this->info("Functionality matrix written to {$path}");

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{title: string, note?: string, rows: array<int, array{feature: string, condition: string, outcome: string}>}>
     */
    private function modules(): array
    {
        return [
            [
                'title' => '1. Employee Birthdays',
                'rows' => [
                    ['feature' => 'Birthday toast on dashboard', 'condition' => 'There are birthdays today.', 'outcome' => "List the employee names with their departments in a toast at the bottom of the dashboard."],
                    ['feature' => 'Birthday toast on dashboard', 'condition' => 'My birthday is today.', 'outcome' => 'Display a toast wishing the logged-in user a happy birthday at the bottom of the dashboard.'],
                    ['feature' => 'Birthday toast on dashboard', 'condition' => 'Both of the above are true.', 'outcome' => 'Both toasts are shown.'],
                    ['feature' => 'Birthday emails', 'condition' => 'There are birthdays today.', 'outcome' => 'All software users are notified about today\'s birthdays by email.'],
                    ['feature' => 'Birthday emails', 'condition' => 'My birthday is today.', 'outcome' => 'The employee receives a "Happy Birthday" email.'],
                    ['feature' => 'Birthday emails', 'condition' => 'There are birthdays tomorrow.', 'outcome' => 'HR receives a reminder email for the upcoming birthdays.'],
                ],
            ],
            [
                'title' => '2. Employee Management',
                'rows' => [
                    ['feature' => 'New user (employee) credential email', 'condition' => 'The Create button on the employee creation form is clicked and registration succeeds. (Email is a required field — the form cannot be submitted without it.)', 'outcome' => 'The new employee receives an email with their login credentials (email, password).'],
                ],
            ],
            [
                'title' => '3. Leave Roster & Leave Management',
                'rows' => [
                    ['feature' => 'Leave application email', 'condition' => 'A leave application form is submitted successfully.', 'outcome' => 'HR, the Head of Division for the applicant\'s division, the Executive Secretary, the Assistant Executive Secretary, and the designated stand-in (cover) all receive an email notification.'],
                    ['feature' => 'Leave approval / rejection', 'condition' => 'An approver (HR, Head of Division, Executive Secretary, or Assistant Executive Secretary) approves or rejects the request at their stage, from the leave detail page or the leave table.', 'outcome' => 'The leave applicant receives an email of the approval or rejection; a rejection includes the stated reason.'],
                    ['feature' => 'HR scheduling leave for staff', 'condition' => 'HR selects a staff member in the "Staff Member" field while applying for leave.', 'outcome' => 'The leave request is created and routed for approval under the selected staff member\'s name, exactly as if that staff member had applied themselves.'],
                    ['feature' => 'HR editing staff leave', 'condition' => 'HR opens a leave request belonging to any staff member that has not yet been approved.', 'outcome' => 'HR can edit and resubmit the request. Once a leave request is approved, editing is locked for everyone, including HR.'],
                ],
            ],
            [
                'title' => '4. Attendance',
                'note' => 'Accessible to HR and the Executive Secretary only.',
                'rows' => [
                    ['feature' => 'Access restriction', 'condition' => 'A user without the HR or Executive Secretary role attempts to open the Attendance module.', 'outcome' => 'Access is denied; the module is hidden from the sidebar and the route is blocked.'],
                    ['feature' => 'Attendance sync', 'condition' => 'A staff member scans their badge at an access-control device.', 'outcome' => 'A timestamped attendance record is created automatically, processed in near real-time (every minute).'],
                    ['feature' => 'Attendance export', 'condition' => 'HR or the Executive Secretary clicks Export on the Attendance page.', 'outcome' => 'A CSV or PDF summary (Staff ID, Name, Department, Date, Clock In, Clock Out, Hours Worked) is downloaded for the selected date range and filters.'],
                ],
            ],
            [
                'title' => '5. Appraisals',
                'rows' => [
                    ['feature' => 'Appraisal submission', 'condition' => 'A regular staff member submits their appraisal.', 'outcome' => 'Their Head of Division (as appraiser) is notified as the pending approver.'],
                    ['feature' => 'Appraisal submission', 'condition' => 'The appraisee is themselves a Head of Division.', 'outcome' => 'The approval stage skips directly to the Executive Secretary (the HR stage is bypassed), who is notified.'],
                    ['feature' => 'Appraisal approval cascade', 'condition' => 'Each stage approver (Head of Division → HR → Executive Secretary) approves.', 'outcome' => 'The appraisee is notified of the approval, and the next-stage approver is notified as the new pending reviewer.'],
                    ['feature' => 'Appraisal rejection', 'condition' => 'Any approver rejects the appraisal.', 'outcome' => 'The appraisal resets to draft for resubmission; the appraisee is notified with the rejection reason.'],
                    ['feature' => 'Appraisal resubmission', 'condition' => 'The appraisee resubmits after a rejection.', 'outcome' => 'The current-stage approver and all HR users are notified.'],
                    ['feature' => 'Appraisal withdrawal', 'condition' => 'The appraisee withdraws a submitted appraisal.', 'outcome' => 'The Head of Division (appraiser) and all HR users are notified.'],
                    ['feature' => 'Appraisal due reminder', 'condition' => 'A pending appraisal passes its staff deadline.', 'outcome' => 'The current-stage approver receives a reminder notification.'],
                    ['feature' => 'Contract-expiry reminder', 'condition' => 'An employee\'s contract end date has passed with no linked appraisal on record.', 'outcome' => 'The employee receives a contract-related appraisal-due notification.'],
                ],
            ],
            [
                'title' => '6. Events',
                'rows' => [
                    ['feature' => 'Event posting', 'condition' => 'An event is created and its target category includes "all users".', 'outcome' => 'Every user in the system is notified.'],
                    ['feature' => 'Event posting', 'condition' => 'An event\'s category targets specific departments, positions, or users.', 'outcome' => 'Only the matching users are notified.'],
                ],
            ],
            [
                'title' => '7. Training & Travel',
                'rows' => [
                    ['feature' => 'Training posting', 'condition' => 'A training is posted without targeting a specific user.', 'outcome' => 'Users matching the training\'s category (or all users) are notified.'],
                    ['feature' => 'Training application', 'condition' => 'A staff member applies for a training.', 'outcome' => 'All HR users are notified.'],
                    ['feature' => 'Training approval', 'condition' => 'HR or the Head of Division approves or rejects a training application.', 'outcome' => 'The status is updated; no notification is sent at this stage.'],
                    ['feature' => 'Training approval', 'condition' => 'The Executive Secretary approves or rejects the application.', 'outcome' => 'The requester is notified of the final decision.'],
                    ['feature' => 'Out-of-station travel clearance', 'condition' => 'A staff member submits an out-of-station/travel clearance naming a stand-in.', 'outcome' => 'The designated stand-in is notified that they are covering for the traveller.'],
                    ['feature' => 'Out-of-station approval', 'condition' => 'HR or the Head of Division approves or rejects.', 'outcome' => 'The status is updated; no notification is sent at this stage.'],
                    ['feature' => 'Out-of-station approval', 'condition' => 'The Executive Secretary approves or rejects.', 'outcome' => 'The requester is notified of the final decision.'],
                ],
            ],
            [
                'title' => '8. Staff Recruitment',
                'note' => 'Available to Heads of Division.',
                'rows' => [
                    ['feature' => 'Recruitment request submission', 'condition' => 'A Head of Division submits a recruitment request.', 'outcome' => 'All HR users are notified.'],
                    ['feature' => 'Recruitment approval cascade', 'condition' => 'HR or the Head of Division approves or rejects.', 'outcome' => 'The status is updated; no notification is sent at this stage.'],
                    ['feature' => 'Recruitment approval cascade', 'condition' => 'The Executive Secretary approves or rejects.', 'outcome' => 'The requester is notified of the final decision.'],
                ],
            ],
            [
                'title' => '9. Job Applications & Screening',
                'rows' => [
                    ['feature' => 'Application submission', 'condition' => 'An applicant submits a job application through the public application link.', 'outcome' => 'The applicant receives a confirmation email.'],
                    ['feature' => 'Automatic screening', 'condition' => 'The job posting has screening criteria configured and the application fails a hard filter (minimum qualification, experience, age range, or a required keyword).', 'outcome' => 'The application is auto-rejected and the reason is recorded on the application. No email is sent (see Status change notification below).'],
                    ['feature' => 'Automatic screening', 'condition' => 'The application passes all configured criteria.', 'outcome' => 'A weighted score (0–100) is calculated across qualification, experience, keyword match, and age fit, and the application is left for review, sortable by score.'],
                    ['feature' => 'Status change notification', 'condition' => 'HR or a Screening Board member marks an application Shortlisted, or advances it to interview, offer, or hire.', 'outcome' => 'The applicant is notified by email.'],
                    ['feature' => 'Status change notification', 'condition' => 'An application is Rejected — automatically by scoring, by HR, or by the Screening Board.', 'outcome' => 'No email is sent to the applicant. Rejection emails to applicants were intentionally disabled.'],
                    ['feature' => 'Screening Board access', 'condition' => 'HR generates a screening link and PIN for a job posting.', 'outcome' => 'Anyone holding the link can enter the PIN to view and shortlist/reject that job\'s applicants only, with no HRMIS account required.'],
                    ['feature' => 'Screening Board access', 'condition' => 'HR regenerates the link/PIN for a job.', 'outcome' => 'The previous link is invalidated and any board sessions already verified against the old PIN are signed out.'],
                    ['feature' => 'Screening Board access', 'condition' => 'A screening PIN is entered incorrectly, repeatedly.', 'outcome' => 'Further attempts are rate-limited.'],
                    ['feature' => 'Applicant export', 'condition' => 'HR clicks Export on the Job Applications page.', 'outcome' => 'A CSV listing each applicant\'s name alongside the job they applied for is downloaded.'],
                ],
            ],
            [
                'title' => '10. Salary Advances',
                'rows' => [
                    ['feature' => 'Application', 'condition' => 'An employee applies for a salary advance.', 'outcome' => 'All HR users are notified.'],
                    ['feature' => 'Approval cascade', 'condition' => 'HR or the Head of Division approves or rejects.', 'outcome' => 'The status is updated; no notification is sent at this stage.'],
                    ['feature' => 'Approval cascade', 'condition' => 'The Executive Secretary approves or rejects.', 'outcome' => 'The employee is notified of the final decision.'],
                ],
            ],
        ];
    }
}
