<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = array(
        Commands\LiveConsole::class,
        Commands\NotificationConsole::class,
        Commands\BepoDBWorkerConsole::class,
        Commands\DBSyncConsole::class,
        Commands\FCMNotificationConsole::class,
	    Commands\Version::class,
	    Commands\CChanel::class
    );

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('bepo:fcmnotify')->everyMinute()->withoutOverlapping();
        $schedule->command('cchanel')->everyMinute()->withoutOverlapping();
    }
}
