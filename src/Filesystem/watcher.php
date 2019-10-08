<?php declare(strict_types=1);

use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Watcher\Factory;

require __DIR__ . '/../../vendor/autoload.php';

$params = array_slice($argv, 1);
$watchList = WatchList::fromJson($params[0]);
$watcher = Factory::create($watchList);

while (true) {
    echo $watcher->findChanges()->hasChanges() ? 1: 0;
    usleep(2500000);
}

