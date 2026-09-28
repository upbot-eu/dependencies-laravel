<?php
namespace Upbot\LaravelDependencies;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

final class UpbotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/upbot.php', 'upbot');
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) return;
        $this->commands([UpbotCommand::class]);
        $this->publishes([__DIR__ . '/../config/upbot.php' => config_path('upbot.php')], 'upbot-config');
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            if (config('upbot.schedule_enabled') && config('upbot.token')) {
                $schedule->command('upbot report')->cron(config('upbot.schedule_cron'))->withoutOverlapping(10);
            }
        });
    }
}
