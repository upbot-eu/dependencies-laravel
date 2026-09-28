# UpBot dependency monitoring for Laravel

[English](README.md) · [Deutsch](README.de.md) · [Slovenčina](README.sk.md)

Laravel 12/13, PHP 8.2+ (Laravel itself may require a newer version). Reuses
`upbot/dependencies`, package discovery, Artisan and the existing scheduler.

Install in the project:

```sh
composer require upbot/laravel-dependencies
```

Add the unique monitoring token to the application's private `.env` or process environment:

```dotenv
UPBOT_TOKEN=replace_with_monitor_token
```

Package discovery loads the defaults and adds daily reporting to the existing
Laravel scheduler. No config publishing or `init` command is required. Install
as a production dependency. Optional settings:

```dotenv
# UPBOT_ENDPOINT=https://app.upbot.eu/api/v1/dependencies/report
# UPBOT_RELEASE=deploy-42
# UPBOT_PRIVATE_PACKAGES=company/internal,@company/private
# UPBOT_SCHEDULE_ENABLED=true
# UPBOT_SCHEDULE_CRON="17 3 * * *"
```

`php artisan upbot init` optionally publishes `config/upbot.php` without overwriting
existing configuration, for projects that need to customize the defaults.

```sh
php artisan config:cache
php artisan upbot doctor
php artisan upbot report
```

All runtime configuration comes from Laravel's config repository, including after
`config:cache`. Rebuild the cache when rotating a token or changing variables.
The adapter reads the application's `.env`; it does not load `.env.upbot`.
`doctor` verifies token/connection and reads lockfiles without sending inventory;
`report` submits it for an asynchronous scan. No token is printed. PHP cURL required.

When a token is configured, one daily `upbot report` event is registered, with
`withoutOverlapping(10)`. Change `UPBOT_SCHEDULE_CRON` or disable it via
`UPBOT_SCHEDULE_ENABLED=false`. The application's scheduler still needs to run:

```cron
* * * * * cd /var/www/project/current && /usr/bin/php artisan schedule:run >> /home/deploy/scheduler.log 2>&1
```

Keep an existing scheduler cron; add this only if it is missing. Confirm with
`php artisan schedule:list`. Set UpBot's expected interval to match. Also run
`php artisan upbot report` after deployment. Store logs outside the web root and
rotate them. For multiple replicas, run the scheduler on one selected instance
or disable this event and invoke the command from the central scheduler.

No request middleware, database migrations or application update/install hooks.
Available lockfiles must describe the active release; the inventory is not an
independent verification of `vendor`/`node_modules`. Supports Composer/npm locks,
including dev dependencies; Yarn/pnpm unsupported. Configure private names in
`UPBOT_PRIVATE_PACKAGES`; their names reach UpBot but are not queried against OSV.

## License

MIT. See [LICENSE](LICENSE).
