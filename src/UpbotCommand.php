<?php
namespace Upbot\LaravelDependencies;

use Illuminate\Console\Command;
use Upbot\Dependencies\Reporter;
use RuntimeException;
use Throwable;

final class UpbotCommand extends Command
{
    protected $signature = 'upbot {action=doctor : init, doctor or report}';
    protected $description = 'Verify UpBot connection or report the installed dependency versions';

    public function handle(): int
    {
        $action = $this->argument('action');
        if ($action === 'init') {
            $this->call('vendor:publish', ['--tag' => 'upbot-config']);
            $this->info('Set UPBOT_TOKEN in the application environment, then run php artisan upbot doctor. Rebuild config:cache after configuration changes.');
            return self::SUCCESS;
        }
        if (!in_array($action, ['doctor', 'report'], true)) {
            $this->error('Supported actions: init, doctor, report.');
            return self::FAILURE;
        }
        try {
            $count = (new Reporter(config('upbot')))->run($action);
            $this->info($action === 'doctor' ? "UpBot: token and connection OK; $count dependency entries readable. No inventory sent." : "UpBot: $count dependency entries accepted.");
            return self::SUCCESS;
        } catch (Throwable $error) {
            $this->error('UpBot: ' . ($error instanceof RuntimeException ? $error->getMessage() : 'Cannot read valid configuration or lockfiles.'));
            return self::FAILURE;
        }
    }
}
