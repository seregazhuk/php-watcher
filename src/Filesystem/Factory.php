<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem;

use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\FsWatchBased\ChangesListener as FsWatchBased;
use seregazhuk\PhpWatcher\Filesystem\ResourceWatcherBased\ChangesListener as ResourceBased;

final class Factory
{
    public static function create(WatchList $watchList, LoopInterface $loop): ChangesListener
    {
        if (FsWatchBased::isAvailable()) {
            return new FsWatchBased($watchList, $loop);
        }

        return new ResourceBased($watchList, $loop);
    }
}
