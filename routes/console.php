<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\ProcessHikvisionLogs;

//send birthday reminders
Schedule::command('reminders:send-birthday')->daily();

//send AppraisalReminder at every 1st of june
Schedule::command('app:appraisal-reminder')->yearlyOn(6, 1, '00:00');

Schedule::command('app:leave-application-due-reminder')->daily();

// Sends rejection emails deferred until a job's application deadline passes
Schedule::command('app:send-deferred-rejection-emails')->hourly();

// Runs every minute — picks up new badge events in near real-time
Schedule::job(new ProcessHikvisionLogs)->everyMinute();


