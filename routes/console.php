<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\RentReminderJob;
use App\Jobs\LeaseExpiryNoticeJob;
use App\Jobs\ServiceChargeCalculationJob;
use App\Jobs\MonthlyReportGenerationJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule rent reminder notifications - Daily at 2:00 PM
Schedule::job(new RentReminderJob)->dailyAt('14:00');

// Schedule lease expiry notices - Daily at 8:00 AM
Schedule::job(new LeaseExpiryNoticeJob)->dailyAt('08:00');

// Schedule monthly service charge calculation - Weekly on Mondays at midnight
Schedule::job(new ServiceChargeCalculationJob)->weeklyOn(1, '00:00');

// Schedule monthly report generation - Monthly on 1st at midnight
Schedule::job(new MonthlyReportGenerationJob)->monthlyOn(1, '00:00');

