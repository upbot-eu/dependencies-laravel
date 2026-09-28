# UpBot: monitoring závislostí pre Laravel

[English](README.md) · [Deutsch](README.de.md) · [Slovenčina](README.sk.md)

Laravel 12/13, PHP 8.2+ (samotný Laravel môže vyžadovať vyššiu verziu).
Používa `upbot/dependencies`, package discovery, Artisan a existujúci scheduler.

**Balík zatiaľ nie je publikovaný v Packagiste.** Po publikovaní:

```sh
composer require upbot/laravel-dependencies
```

Do súkromného Laravel `.env` alebo prostredia procesu pridaj token sledovania:

```dotenv
UPBOT_TOKEN=replace_with_monitor_token
```

Package discovery načíta predvolené nastavenia a pridá denné odosielanie
do existujúceho scheduleru. Publikovanie konfigurácie ani `init` nie sú povinné.
Balík inštaluj ako produkčnú závislosť. Voliteľné nastavenia:

```dotenv
# UPBOT_ENDPOINT=https://app.upbot.eu/api/v1/dependencies/report
# UPBOT_RELEASE=deploy-42
# UPBOT_PRIVATE_PACKAGES=company/internal,@company/private
# UPBOT_SCHEDULE_ENABLED=true
# UPBOT_SCHEDULE_CRON="17 3 * * *"
```

Pred publikovaním môžu vývojári nastaviť path repositories pre tento balík
a `upbot/dependencies` s verziou `0.1.0` v `options.versions`.
Voliteľný `php artisan upbot init` publikuje `config/upbot.php` bez prepísania
existujúcej konfigurácie, ak projekt potrebuje upraviť predvolené nastavenia.

```sh
php artisan config:cache
php artisan upbot doctor
php artisan upbot report
```

## Overenie a konfigurácia

Všetka runtime konfigurácia pochádza z Laravel config repository, aj po
`config:cache`. Pri obnove tokenu či zmene premenných obnov cache.
Adaptér používa aplikačný `.env`; `.env.upbot` nenačíta. `doctor` overí token
a spojenie a načíta lock súbory bez odoslania inventára. `report` ho odošle
na asynchrónnu kontrolu. Token sa nevypisuje. PHP vyžaduje cURL.

## Scheduler a nasadenie

Po nastavení tokenu sa zaregistruje denné `upbot report` s
`withoutOverlapping(10)`. Čas zmeníš cez `UPBOT_SCHEDULE_CRON`, prípadne odosielanie
vypneš cez `UPBOT_SCHEDULE_ENABLED=false`. Aplikačný scheduler musí byť spustený:

```cron
* * * * * cd /var/www/project/current && /usr/bin/php artisan schedule:run >> /home/deploy/scheduler.log 2>&1
```

Ak scheduler cron už existuje, zachovaj ho; nový pridaj iba ak chýba.
Over ho cez `php artisan schedule:list`. Očakávaný interval v UpBote nastav
rovnako. Po nasadení spusti aj `php artisan upbot report`. Logy uchovávaj
mimo webrootu a rotuj ich. Pri viacerých replikách spúšťaj scheduler na jednej
z nich alebo vypni túto udalosť a príkaz spúšťaj z centrálneho scheduleru.

## Rozsah

Balík nepridáva request middleware, databázové migrácie ani automatické
inštalačné či aktualizačné hooky. Dostupné lock súbory musia opisovať aktívny
release; inventár nie je nezávislé overenie `vendor` či `node_modules`.
Podporuje Composer/npm lock súbory vrátane dev závislostí; Yarn/pnpm nie sú
podporované. Privátne názvy nastav v `UPBOT_PRIVATE_PACKAGES`; odošlú sa UpBotu,
ale nie do OSV. Pre každú aplikáciu a prostredie používaj samostatné sledovanie
a token.

## Licencia

MIT. Pozri [LICENSE](LICENSE).
