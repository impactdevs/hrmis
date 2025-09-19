<?php
// Temporary debug script - run this in tinker or as a route
// php artisan tinker, then paste this code

$userEmail = 'your_staff_email@domain.com'; // Replace with actual staff email

echo "=== APPRAISAL CREATION DEBUG ===\n\n";

// 1. Check user
$user = \App\Models\User::where('email', $userEmail)->first();
if (!$user) {
    echo "❌ USER NOT FOUND: $userEmail\n";
    return;
}

echo "✅ User found: {$user->email}\n";
echo "   - ID: {$user->id}\n";
echo "   - Email verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
echo "   - Data usage agreed: " . ($user->agreed_to_data_usage ? 'Yes' : 'No') . "\n";

// 2. Check roles
$roles = $user->getRoleNames();
echo "   - Roles: " . $roles->join(', ') . "\n\n";

if (!$roles->contains('Staff')) {
    echo "❌ USER MUST HAVE 'Staff' ROLE\n";
    echo "   Current roles: " . $roles->join(', ') . "\n";
    return;
}

// 3. Check employee record
$employee = \App\Models\Employee::withoutGlobalScopes()
    ->where('email', $userEmail)
    ->first();

if (!$employee) {
    echo "❌ NO EMPLOYEE RECORD FOUND\n";
    return;
}

echo "✅ Employee record found: {$employee->first_name} {$employee->last_name}\n";
echo "   - Employee ID: {$employee->employee_id}\n";
echo "   - Department ID: {$employee->department_id}\n";

// 4. Check department
if (!$employee->department_id) {
    echo "❌ EMPLOYEE NOT ASSIGNED TO DEPARTMENT\n";
    return;
}

$department = $employee->department;
if (!$department) {
    echo "❌ DEPARTMENT NOT FOUND\n";
    return;
}

echo "✅ Department: {$department->department_name}\n";
echo "   - Department Head ID: {$department->department_head}\n";

// 5. Check department head
if (!$department->department_head) {
    echo "❌ DEPARTMENT HAS NO HEAD ASSIGNED\n";
    return;
}

$departmentHead = \App\Models\User::find($department->department_head);
if (!$departmentHead) {
    echo "❌ DEPARTMENT HEAD USER NOT FOUND\n";
    return;
}

echo "✅ Department Head: {$departmentHead->email}\n";

$headRoles = $departmentHead->getRoleNames();
echo "   - Head Roles: " . $headRoles->join(', ') . "\n";

$validHeadRole = $departmentHead->hasRole('Head of Division') || $departmentHead->hasRole('HR');
if (!$validHeadRole) {
    echo "❌ DEPARTMENT HEAD MUST HAVE 'Head of Division' OR 'HR' ROLE\n";
    echo "   Current roles: " . $headRoles->join(', ') . "\n";
    return;
}

// 6. Check appraiser assignment
$headEmployee = $departmentHead->employee;
if (!$headEmployee) {
    echo "❌ DEPARTMENT HEAD HAS NO EMPLOYEE RECORD\n";
    return;
}

echo "✅ Appraiser will be: {$headEmployee->first_name} {$headEmployee->last_name}\n";
echo "   - Appraiser ID: {$headEmployee->employee_id}\n";

echo "\n🎉 ALL CHECKS PASSED - APPRAISAL CREATION SHOULD WORK!\n";
echo "\nIf it's still not working, check:\n";
echo "- Browser console for JavaScript errors\n";
echo "- Laravel logs for any exceptions\n";
echo "- Network tab for failed requests\n";
