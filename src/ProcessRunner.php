<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use React\ChildProcess\Process as ReactPHPProcess;
use React\EventLoop\LoopInterface;
use RuntimeException;
use seregazhuk\PhpWatcher\Screen\Screen;

final class ProcessRunner
{
    private $loop;

    private $screen;

    private $process;

    public function __construct(LoopInterface $loop, Screen $screen, string $command)
    {
        $this->loop = $loop;
        $this->screen = $screen;
        $this->process = new ReactPHPProcess($command);
    }

    public function start(): void
    {
        $this->screen->start($this->process->getCommand());
        $this->screen->showSpinner($this->loop);

        $this->process->start($this->loop);
        $this->subscribeToProcessOutput();
    }

    public function stop(int $signal): void
    {
        $this->process->terminate($signal);
        $this->process->removeAllListeners();
    }

    public function restart(float $delayToRestart): void
    {
        $this->screen->restarting($this->process->getCommand());
        $this->loop->addTimer($delayToRestart, [$this, 'start']);
    }

    private function subscribeToProcessOutput(): void
    {
        if ($this->process->stdout === null || $this->process->stderr === null) {
            throw new RuntimeException('Cannot open I/O for a process');
        }

        $this->process->stdout->on('data', [$this->screen, 'plainOutput']);
        $this->process->stderr->on('data', [$this->screen, 'plainOutput']);
        $this->process->on('exit', [$this->screen, 'processExit']);
    }
}
