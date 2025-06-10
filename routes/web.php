<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CompanyJobController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Form\EntryController;
use App\Http\Controllers\Form\FormController;
use App\Http\Controllers\Form\FormFieldController;
use App\Http\Controllers\Form\FormSettingController;
use App\Http\Controllers\Form\SectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveRosterController;
use App\Http\Controllers\LeaveTypesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OutOfStationTrainingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicHolidayController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffRecruitmentController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\UploadEmployees;
use App\Http\Controllers\UsersController;
use App\Models\Employee;
use App\Models\StaffRecruitment;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('dashboard');
});

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/upload-employee', [UploadEmployees::class, 'process_csv_for_arrears']);
Route::get('/employees/{employee}/generate-pdf', [EmployeeController::class, 'generatePDF'])
    ->name('employees.generate-pdf');
Route::get('/employees/{employee}/print', function (Employee $employee) {
    return view('employees.pdf', ['employee' => $employee]);
})->name('employees.print');

Route::get('/send-email', [AppraisalController::class, 'sendEmails'])
    ->name('send-email');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UsersController::class);
    Route::post('roles/{role}/permissions/add', [RoleController::class, 'addPermissions'])->name('roles.permissions.add');
    Route::post('roles/{role}/permissions/remove', [RoleController::class, 'removePermissions'])->name('roles.permissions.remove');

    Route::resource('employees', EmployeeController::class);
    Route::get('/contract/{employee_id}/create', [EmployeeController::class, 'create_contract'])->name('contract.create');
    Route::get('/contract/{contract}/edit', [EmployeeController::class, 'edit_contract'])->name('contract.edit');
    Route::put('/contract/{contract}/update', [EmployeeController::class, 'update_contract'])->name('contract.update');
    Route::delete('/contract/{contract}/delete', [EmployeeController::class, 'destroy_contract'])->name('contract.destroy');




    Route::post('/contract', [EmployeeController::class, 'store_contract'])->name('contract.store');
    Route::get('/leave-management/data', [LeaveController::class, 'getLeaveManagementData']);
    Route::post('update-entitled-leave-days/{id}', [EmployeeController::class, 'updateEntitledLeaveDays'])->name('update-entitled-leave-days');
    Route::resource('recruitments', StaffRecruitmentController::class);
    Route::post('/recruitments/{recruitment}/status', [StaffRecruitmentController::class, 'approveOrReject'])
        ->name('recruitmentments.approveOrReject');
    Route::resource('appraisals', AppraisalController::class);
    Route::post('/appraisal/appraisal-approval', [AppraisalController::class, 'approveOrReject'])
        ->name('appraisals.approveOrReject');
    Route::post('/appraisals/{appraisal}/status', [AppraisalController::class, 'approveOrReject'])
        ->name('appraisals.status');
    Route::get('/appraisals/{appraisal}/download', [AppraisalController::class, 'downloadPDF'])
        ->name('appraisals.download');
    Route::resource('events', EventController::class);
    Route::resource('trainings', TrainingController::class);
    Route::resource('out-of-station-trainings', OutOfStationTrainingController::class);
    Route::post('/out-of-station-trainings/{training}/status', [OutOfStationTrainingController::class, 'approveOrReject'])
        ->name('out-of-station-trainings.approveOrReject');
    Route::get('training-application', [TrainingController::class, 'apply'])->name('apply');
    Route::post('save-training-application', [TrainingController::class, 'applyTraining'])->name('save.apply');
    Route::post('/trainings/{training}/status', [TrainingController::class, 'approveOrReject'])
        ->name('trainings.approveOrReject');
    Route::resource('positions', PositionController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('leaves', LeaveController::class);
    //leave actions
    Route::post('/leaves/{leave}/status', [LeaveController::class, 'approveOrReject'])
        ->name('leaves.approveOrReject');
    Route::post('save-leave-data', [LeaveRosterController::class, 'saveLeaveRosterData'])->name('save-leave-data');
    Route::resource('leave-roster', LeaveRosterController::class);
    Route::get('/leave-roster-calendar-data', [LeaveRosterController::class, 'leaveRosterCalendarData'])->name('leave-roster.calendarData');
    Route::get('/leave-data', [LeaveController::class, 'leaveData'])->name('leave.data');
    Route::get('/leave-roster-tabular', [LeaveRosterController::class, 'getLeaveRoster'])->name('leave-roster-tabular.index');
    Route::get('/leave-roster-tabular/data', [LeaveRosterController::class, 'getLeaveRosterData'])->name('leave-roster-tabular.data');
    Route::resource('leave-types', LeaveTypesController::class);
    Route::resource('public_holidays', PublicHolidayController::class);
    Route::post('calender', [leaveRosterController::class, 'getcalender']);
    Route::get('leave-management', [LeaveController::class, 'leaveManagement'])->name('leave-management');
    Route::get('apply-for-leave/{leaveRoster}', [LeaveController::class, 'applyForLeave'])->name('apply-for-leave');
    Route::resource('company-jobs', CompanyJobController::class);
    Route::resource('departments', DepartmentController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //appraisal
    Route::get('/employee-appraisal', [AppraisalController::class, 'survey'])->name('appraisal.survey');
    Route::post('/appraisals', [AppraisalController::class, 'store'])->name('appraisals.store');
    Route::get('preview-appraisal/{appraisal}', [AppraisalController::class, 'previewAppraisalDetails'])->name('appraisals.preview');

    //notifications
    Route::resource('/notifications', NotificationController::class);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');

    Route::get('/get-count', [NotificationController::class, 'getCount']);

    Route::get('/uncst-matrix', [DocumentController::class, 'uncst_matrix'])->name('uncst-matrix');

    Route::resource('applications', JobApplicationController::class)
        ->except(['create']); // exclude create because it's public
});

Route::get('job-applications/create', [JobApplicationController::class, 'create'])->name('job-applications.create');



Route::get('/import', [EmployeeController::class, 'import_employees']);

require __DIR__ . '/auth.php';
