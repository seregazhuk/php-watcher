<?php declare(strict_types=1);

use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Watcher\Factory;

if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
	require __DIR__ . '/../../vendor/autoload.php';
} else {
	require __DIR__.'/../../../../autoload.php';
}

$params = array_slice($_SERVER['argv'], 1);
$watchList = WatchList::fromJson($params[0]);
$watcher = Factory::create($watchList);

while (true) {
    echo $watcher->findChanges()->hasChanges() ? 1: 0;
    usleep(1500000);
}

