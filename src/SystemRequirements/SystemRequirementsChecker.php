<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\SystemRequirements;

use Seregazhuk\ReactFsWatch\FsWatch;
use Symfony\Component\Process\Process;

final class SystemRequirementsChecker
{
    public static function isFsWatchAvailable(): bool
    {
        return FsWatch::isAvailable();
    }

    public static function isNodeJsInstalled(): bool
    {
        $process = new Process(command: ['node', '-v']);
        $process->start();
        $process->wait();

        return $process->getExitCode() === 0;
    }

    public static function isChokidarInstalled(): bool
    {
        $process = new Process(command: ['npm', 'list', 'chokidar']);
        $process->start();
        $process->wait();

        return $process->getExitCode() === 0 && str_contains($process->getOutput(), 'chokidar');
    }

    public static function installChokidar(): void
    {
        $process = new Process(command: ['npm', 'install', 'chokidar']);
        $process->start();
        $process->wait();
    }
}
