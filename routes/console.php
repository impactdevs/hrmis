<?php

use Illuminate\Support\Facades\Schedule;

//send birthday reminders
Schedule::command('reminders:send-birthday')->daily();

//send AppraisalReminder at every 1st of june
Schedule::command('reminders:send-appraisal')->yearlyOn(6, 1);

