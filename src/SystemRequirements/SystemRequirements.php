<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\SystemRequirements;

use seregazhuk\PhpWatcher\Screen\Screen;
use Symfony\Component\Process\Process;

final class SystemRequirements
{
    public function __construct(private readonly Screen $screen) {}

    public function check(): bool
    {
        if ($this->isNodeJsInstalled() === false) {
            $this->screen->warning('Node.js is not installed.');
            $this->screen->warning('Please install it from https://nodejs.org/en/download/');

            return false;
        }

        if ($this->isChokidarInstalled() === false) {
            $this->screen->comment('Chokidar is not installed in the project.');
            $this->screen->comment('Installing chokidar...');
            $this->installChokidar();
        }

        return true;
    }

    public function isNodeJsInstalled(): bool
    {
        $process = new Process(command: ['node', '-v']);
        $process->start();
        $process->wait();

        return $process->getExitCode() === 0;
    }

    public function isChokidarInstalled(): bool
    {
        $process = new Process(command: ['npm', 'list', 'chokidar']);
        $process->start();
        $process->wait();

        return $process->getExitCode() === 0 && str_contains($process->getOutput(), 'chokidar');
    }

    public function installChokidar(): void
    {
        $process = new Process(command: ['npm', 'install', 'chokidar']);
        $process->start();
        $process->wait();
    }
}
