<?php

use App\Models\Employee;
use App\Models\LeaveRoster;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('roster.{employeeId}', function (User $user, string $employeeId) {
    return $user->employee->employee_id === Employee::find($employeeId)->employee_id;
});
