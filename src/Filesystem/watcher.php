<?php declare(strict_types=1);

use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ResourceWatcherFactory;

require_composer_autoloader();

$params = array_slice($_SERVER['argv'], 1);
$watchList = WatchList::fromJson($params[0]);
$watcher = ResourceWatcherFactory::create($watchList);

while (true) {
    echo $watcher->findChanges()->hasChanges() ? 1 : 0;
    usleep(1500000);
}

function require_composer_autoloader(): void
{
    $paths = [
        '/../../vendor/autoload.php',
        '/../../vendor/autoload.php',
    ];

    foreach ($paths as $path) {
        if (file_exists(__DIR__ . $path)) {
            require __DIR__ . $path;
            return;
        }
    }

    throw new RuntimeException('Cannot find Composer autoloader');
}
