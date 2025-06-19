<?php

use Illuminate\Support\Facades\Schedule;

//send birthday reminders
Schedule::command('reminders:send-birthday')->daily();

//send AppraisalReminder at every 1st of june
Schedule::command('app:appraisal-reminder')->yearlyOn(6, 1, '00:00');

Schedule::command('app:leave-application-due-reminder')->daily('14:07');

