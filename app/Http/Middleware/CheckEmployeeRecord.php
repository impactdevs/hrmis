<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class CheckEmployeeRecord
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();



        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $employee = Employee::withoutGlobalScopes()
            ->where('email', $user->email)
            ->first();




        if (!$employee) {
            abort(403, 'No employee record found for this user. contact the human resource department to create an employee record with your email address.');
        }

        // look for the user with the email and check if the user_id in employee table matches with the id, if not update it
        $user_id = $user->id;
        if ($employee->user_id !== $user_id) {
            $employee->user_id = $user_id;
            $employee->save();
        }

        return $next($request);
    }
}
