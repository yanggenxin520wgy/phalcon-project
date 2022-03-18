<?php

use Project\Core\Version\Version;
use Project\Core\Utils\Color;
use Phalcon\Script;
use Project\Core\Commands\Builtin\Info;
use Project\Core\Commands\Builtin\Model;
use Project\Core\Commands\Builtin\Module;
use Project\Core\Commands\Builtin\Project;
use Project\Core\Commands\CommandsListener;
use Project\Core\Commands\Builtin\AllModels;
use Project\Core\Commands\Builtin\Migration;
use Project\Core\Commands\Builtin\Enumerate;
use Project\Core\Commands\Builtin\Controller;
use Phalcon\Commands\DotPhalconMissingException;
use Phalcon\Events\Manager as EventsManager;

try {
    require 'autoload.php';
    $vendor = sprintf('Project Version (%s)', Version::get());
    print PHP_EOL . Color::colorize($vendor, Color::FG_GREEN, Color::AT_BOLD) . PHP_EOL . PHP_EOL;

    $eventsManager = new EventsManager();

    $eventsManager->attach('command', new CommandsListener());

    $script = new Script($eventsManager);

    $commandsToEnable = [
        Info::class,
        Enumerate::class,
        Controller::class,
        Module::class,
        Model::class,
        AllModels::class,
        Project::class,
        Migration::class,
    ];

    foreach ($commandsToEnable as $command) {
        $script->attach(new $command($script, $eventsManager));
    }

    $script->run();

} catch (DotPhalconMissingException $e) {
    fwrite(STDERR, Color::info($e->getMessage() . " " . $e->scanPathMessage()));
    if ($e->promptResolution()) {
        $script->run();
    } else {
        exit(1);
    }
} catch (Exception $e) {
    fwrite(STDERR, Color::error($e->getMessage()) . PHP_EOL);
    exit(1);
}

