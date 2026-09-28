# UpBot: Abhängigkeitsmonitoring für Laravel

[English](README.md) · [Deutsch](README.de.md) · [Slovenčina](README.sk.md)

Laravel 12/13, PHP 8.2+ (Laravel selbst kann eine neuere Version verlangen).
Verwendet `upbot/dependencies`, Package Discovery, Artisan und den vorhandenen Scheduler.

**Die Veröffentlichung auf Packagist steht noch aus.** Danach:

```sh
composer require upbot/laravel-dependencies
```

Das Token dieses Monitors in die private Laravel-`.env` oder Prozessumgebung eintragen:

```dotenv
UPBOT_TOKEN=replace_with_monitor_token
```

Package Discovery lädt die Standardkonfiguration und ergänzt tägliche Berichte
im vorhandenen Scheduler. Eine Veröffentlichung der Konfiguration oder `init`
ist nicht erforderlich. Als Produktionsabhängigkeit installieren. Optionale Einstellungen:

```dotenv
# UPBOT_ENDPOINT=https://app.upbot.eu/api/v1/dependencies/report
# UPBOT_RELEASE=deploy-42
# UPBOT_PRIVATE_PACKAGES=company/internal,@company/private
# UPBOT_SCHEDULE_ENABLED=true
# UPBOT_SCHEDULE_CRON="17 3 * * *"
```

Bis zur Veröffentlichung können Entwickler Path-Repositories für dieses Paket
und `upbot/dependencies` mit Version `0.1.0` in `options.versions` konfigurieren.
Optional veröffentlicht `php artisan upbot init` die Datei `config/upbot.php`,
ohne vorhandene Konfiguration zu überschreiben, falls projektspezifische
Anpassungen erforderlich sind.

```sh
php artisan config:cache
php artisan upbot doctor
php artisan upbot report
```

## Prüfung und Konfiguration

Die gesamte Laufzeitkonfiguration stammt aus Laravels Konfigurationsrepository,
auch nach `config:cache`. Bei Tokenwechsel oder geänderten Variablen den Cache
neu erstellen. Der Adapter verwendet die Anwendungs-`.env`, nicht `.env.upbot`.
`doctor` prüft Token und Verbindung und liest Lockdateien, ohne das Inventar
zu senden. `report` übermittelt es für eine asynchrone Prüfung. Das Token wird
nicht ausgegeben. Die PHP-cURL-Erweiterung ist erforderlich.

## Scheduler und Deployment

Mit konfiguriertem Token wird ein täglicher `upbot report`-Termin mit
`withoutOverlapping(10)` registriert. Den Zeitpunkt über `UPBOT_SCHEDULE_CRON`
ändern oder mit `UPBOT_SCHEDULE_ENABLED=false` deaktivieren. Der Anwendungsscheduler
muss weiterhin laufen:

```cron
* * * * * cd /var/www/project/current && /usr/bin/php artisan schedule:run >> /home/deploy/scheduler.log 2>&1
```

Einen vorhandenen Scheduler-Cronjob beibehalten; nur ergänzen, falls er fehlt.
Mit `php artisan schedule:list` prüfen. Das erwartete Intervall in UpBot passend
einstellen. Nach dem Deployment zusätzlich `php artisan upbot report` ausführen.
Logs außerhalb des Webverzeichnisses speichern und rotieren. Bei mehreren Replikas
den Scheduler auf einer ausgewählten Instanz ausführen oder diesen Termin
deaktivieren und den Befehl über einen zentralen Scheduler starten.

## Umfang

Keine Request-Middleware, Datenbankmigrationen oder automatischen Installations-
und Update-Hooks. Verfügbare Lockdateien müssen das aktive Release beschreiben;
das Inventar ist keine unabhängige Prüfung von `vendor` oder `node_modules`.
Composer/npm-Lockdateien einschließlich Entwicklungsabhängigkeiten werden
unterstützt, Yarn/pnpm nicht. Private Namen in `UPBOT_PRIVATE_PACKAGES`
konfigurieren; sie erreichen UpBot, werden aber nicht an OSV gesendet.
Für jede Anwendung und Umgebung einen eigenen Monitor mit eigenem Token verwenden.

## Lizenz

MIT. Siehe [LICENSE](LICENSE).
