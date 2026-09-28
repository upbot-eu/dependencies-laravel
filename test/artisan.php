<?php
// A real Laravel fixture in a disposable directory; never boots a customer's app or DB.
require getenv('UPBOT_TEST_PHP_ROOT') . '/vendor/autoload.php';
$app = Illuminate\Foundation\Application::configure(basePath: getcwd())->create();
exit($app->handleCommand(new Symfony\Component\Console\Input\ArgvInput));
