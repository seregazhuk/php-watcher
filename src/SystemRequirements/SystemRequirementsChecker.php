<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\SystemRequirements;

use Symfony\Component\Process\Process;

final class SystemRequirementsChecker
{
    /**
     * Works correctly only on OSX.
     */
    public static function isFSWatchAvailable(): bool
    {
        $process = new Process(command: ['fswatch', '--version']);
        $process->start();
        $process->wait();

        return $process->isSuccessful() && PHP_OS_FAMILY === 'Darwin';
    }

    public static function isNodeJsInstalled(): bool
    {
        $process = new Process(command: ['node', '-v']);
        $process->start();
        $process->wait();

        return $process->isSuccessful();
    }

    public static function isChokidarInstalled(): bool
    {
        $process = new Process(command: ['npm', 'list', 'chokidar']);
        $process->start();
        $process->wait();

        return $process->isSuccessful() && str_contains($process->getOutput(), 'chokidar');
    }

    public static function installChokidar(): void
    {
        $process = new Process(command: ['npm', 'install', 'chokidar']);
        $process->start();
        $process->wait();
    }
}
