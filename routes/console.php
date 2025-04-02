<?php

use Illuminate\Support\Facades\Schedule;

//send birthday reminders
Schedule::command('reminders:send-birthday')->daily()->at('14:48');
