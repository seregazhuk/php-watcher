<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Watcher;

use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\ScriptToRun;
use seregazhuk\PhpWatcher\Screen;
use Symfony\Component\Process\Process;
use Yosymfony\ResourceWatcher\ResourceWatcher;

final class Watcher
{
    private $loop;

    private $resourceWatcher;

    private $screen;

    public function __construct(LoopInterface $loop, ResourceWatcher $resourceWatcher, Screen $screen)
    {
        $this->loop = $loop;
        $this->resourceWatcher = $resourceWatcher;
        $this->screen = $screen;
    }

    public function startWatching(ScriptToRun $scriptToRun): void
    {
        $process = new Process($scriptToRun->command);
        $this->screen->start($process->getCommandLine());
        $process->start();

        $this->loop->addPeriodicTimer($scriptToRun->delay, function () use ($process) {
            echo $process->getIncrementalOutput();
            if ($this->shouldReload()) {
                $this->restartProcess($process);
            }
        });

        $this->loop->run();
    }

    private function restartProcess(Process $process): void
    {
        $process->stop();
        $process->start();
        $this->screen->restarting($process->getCommandLine());
    }

    private function shouldReload(): bool
    {
        $changeSet = $this->resourceWatcher->findChanges();

        return $changeSet->hasChanges();
    }
}
