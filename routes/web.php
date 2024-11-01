<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CompanyJobController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Form\EntryController;
use App\Http\Controllers\Form\FormController;
use App\Http\Controllers\Form\FormFieldController;
use App\Http\Controllers\Form\FormSettingController;
use App\Http\Controllers\Form\SectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('dashboard');
});

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UsersController::class);
    Route::post('roles/{role}/permissions/add', [RoleController::class, 'addPermissions'])->name('roles.permissions.add');
    Route::post('roles/{role}/permissions/remove', [RoleController::class, 'removePermissions'])->name('roles.permissions.remove');


    Route::resource('employees', EmployeeController::class);
    Route::resource('appraisals', AppraisalController::class);
    Route::resource('events', EventController::class);
    Route::resource('trainings', TrainingController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('leaves', LeaveController::class);
    Route::resource('leave-types', LeaveTypesController::class);
    Route::resource('company-jobs', CompanyJobController::class);
    Route::resource('departments', DepartmentController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('fields', FormFieldController::class);
    Route::post('add-condition', [FormFieldController::class, 'addConditionalVisibilityField'])->name('fields.add-condition');
    Route::get('/get-condition/{field_id}', [FormFieldController::class, 'getConditionalVisibilityField'])->name('fields.get-condition');



    Route::resource('form-builder', FormController::class);
    Route::resource('forms', FormController::class);
    Route::get('/forms/{form}', [FormController::class, 'display_questionnaire'])->name('forms.show');

    Route::resource('sections', SectionController::class);


    Route::resource('entries', EntryController::class);
    Route::get('/forms/{form}/entries', [EntryController::class, 'entries'])->name('forms.entries');
    Route::post('entry-update/{id}', [EntryController::class, 'entry_update'])->name('entry.update-up');
    Route::post('/save-draft', [EntryController::class, 'store'])->middleware('auth')->name('save-draft');
    Route::get('/forms/survey/{form}/{user}', [EntryController::class, 'survey'])->name('form.survey');

    Route::get('/forms/{form}/settings', [FormSettingController::class, 'index'])->name('forms.settings');
    Route::put('/update-settings', [FormSettingController::class, 'update'])->name('form-settings.update');


    //applications
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/unst-job-application', [ApplicationController::class, 'survey'])->name('application.survey');
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');


    //appraisal
    Route::get('/employee-appraisal', [AppraisalController::class, 'survey'])->name('appraisal.survey');
    Route::post('/appraisals', [AppraisalController::class, 'store'])->name('appraisals.store');

    //notifications
    Route::resource('/notifications', NotificationController::class);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');

    Route::get('/get-count', [NotificationController::class, 'getCount']);

    //leave actions
    Route::post('/leaves/{leave}/status', [LeaveController::class, 'approveOrReject'])
        ->name('leaves.approveOrReject');

});

require __DIR__ . '/auth.php';
