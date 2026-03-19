<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SuperAdminController as SuperAdminController;
class CheckInCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckInCron:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('CheckInCron:Cron Command Run start!');
		$checkin = new SuperAdminController();
		$checkin->checkinUrlApi();
		$this->info('CheckInCron:Cron Command Run successfully!');
    }
}
