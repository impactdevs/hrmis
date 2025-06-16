<?php

use Illuminate\Support\Facades\Schedule;

//send birthday reminders
Schedule::command('reminders:send-birthday')->daily();

//send AppraisalReminder at every 1st of june
Schedule::command('app:appraisal-reminder')->dailyAt('19:05');

