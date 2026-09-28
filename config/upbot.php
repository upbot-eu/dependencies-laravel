<?php
return [
    'token' => env('UPBOT_TOKEN'),
    'endpoint' => env('UPBOT_ENDPOINT', 'https://app.upbot.eu/api/v1/dependencies/report'),
    'projectDir' => env('UPBOT_PROJECT_DIR', base_path()),
    'release' => env('UPBOT_RELEASE'),
    'privatePackages' => array_values(array_filter(array_map('trim', explode(',', env('UPBOT_PRIVATE_PACKAGES', ''))))),
    'schedule_enabled' => env('UPBOT_SCHEDULE_ENABLED', true),
    'schedule_cron' => env('UPBOT_SCHEDULE_CRON', '17 3 * * *'),
];
