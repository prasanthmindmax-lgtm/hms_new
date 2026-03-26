<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	protected $commands = [
			Commands\CheckInCron::class,
      Commands\BilllistApi::class,
      Commands\SendDeadlineNotifications::class,
		];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
      $schedule->command('CheckInCron:cron')->dailyAt('23:59');
      // $schedule->command('fetch:api-data')->dailyAt('10:55');
       $schedule->command('fetch:api-data')->dailyAt('10:11')->timezone('Asia/Kolkata')->withoutOverlapping();
       $schedule->command('fetch:api-data-new')->dailyAt('15:14')->timezone('Asia/Kolkata')->withoutOverlapping();
       $schedule->command('notify:deadlines')->dailyAt('14:47')->timezone('Asia/Kolkata')->withoutOverlapping()
             ->appendOutputTo(storage_path('logs/deadline-notifications.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
