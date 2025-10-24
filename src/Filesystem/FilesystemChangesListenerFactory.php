<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem;

use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener\ChangesListenerInterface;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener\ChokidarChangesListener;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener\FSWatchChangesListener;
use seregazhuk\PhpWatcher\Screen\Screen;
use seregazhuk\PhpWatcher\SystemRequirements\SystemRequirementsChecker;
use seregazhuk\PhpWatcher\SystemRequirements\SystemRequirementsNotMetException;

final class FilesystemChangesListenerFactory
{
    public static function create(LoopInterface $loop, Screen $screen): ChangesListenerInterface
    {
        if (SystemRequirementsChecker::isFsWatchAvailable()) {
            return new FsWatchChangesListener($loop);
        }

        if (SystemRequirementsChecker::isNodeJsInstalled()) {
            if (SystemRequirementsChecker::isChokidarInstalled() === false) {
                $screen->comment('Chokidar is not installed in the project.');
                $screen->comment('Installing chokidar...');
                SystemRequirementsChecker::installChokidar();
            }

            return new ChokidarChangesListener($loop);
        }

        throw new SystemRequirementsNotMetException('Neither Node.js nor fswatch are installed.');
    }
}
