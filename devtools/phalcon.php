#!/usr/bin/env php
<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Developer Tools                                                |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2015 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

use Phalcon\Script;
use Phalcon\Loader;
use Phalcon\Version;
use Phalcon\Script\Color;
use Phalcon\Commands\Builtin\Model;
use Phalcon\Commands\Builtin\Module;
use Phalcon\Commands\Builtin\Project;
use Phalcon\Commands\Builtin\Scaffold;
use Phalcon\Commands\CommandsListener;
use Phalcon\Commands\Builtin\Webtools;
use Phalcon\Commands\Builtin\AllModels;
use Phalcon\Commands\Builtin\Migration;
use Phalcon\Commands\Builtin\Enumerate;
use Phalcon\Commands\Builtin\Controller;
use Phalcon\Exception as PhalconException;
use Phalcon\Events\Manager as EventsManager;

try {
    if (!extension_loaded('phalcon7')) {
        throw new Exception(
            "Phalcon7 extension isn't installed, follow these instructions to install it: " .
            'https://docs.phalconphp.com/en/latest/reference/install.html'
        );
    }

    $loader = new Loader();
    $loader
        ->registerDirs(array(__DIR__ . '/scripts/'))
        ->registerNamespaces(array('Phalcon' => __DIR__ . '/scripts/'))
        ->register();

    if (file_exists('.phalcon/autoload.php')) {
        require_once '.phalcon/autoload.php';
    }

    if (Version::getId() < Script::COMPATIBLE_VERSION) {
        throw new Exception(
            sprintf(
                "Your Phalcon version isn't compatible with Developer Tools, download the latest at: %s",
                Script::DOC_DOWNLOAD_URL
            )
        );
    }

    defined('TEMPLATE_PATH') || define('TEMPLATE_PATH', __DIR__ . '/templates');

    $vendor = sprintf('Phalcon7 DevTools (%s)', Version::get());
    print PHP_EOL . Color::colorize($vendor, Color::FG_GREEN, Color::AT_BOLD) . PHP_EOL . PHP_EOL;

    $eventsManager = new EventsManager();

    $eventsManager->attach('command', new CommandsListener());

    $script = new Script($eventsManager);

    $commandsToEnable = [
        Enumerate::class,
        Controller::class,
        Module::class,
        Model::class,
        AllModels::class,
        Project::class,
        Scaffold::class,
        Migration::class,
        Webtools::class,
    ];

    $script->loadUserScripts();

    foreach ($commandsToEnable as $command) {
        $script->attach(new $command($script, $eventsManager));
    }

    $script->run();
} catch (PhalconException $e) {
    fwrite(STDERR, Color::error($e->getMessage()) . PHP_EOL);
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
