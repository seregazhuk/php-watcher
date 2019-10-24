<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Screen;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\ConsoleApplication;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Screen
{
    private $output;

    private $spinner;

    public function __construct(SymfonyStyle $output)
    {
        $this->output = $output;
        $this->spinner = new Spinner();
    }

    public function showOptions(WatchList $watchList): void
    {
        $this->title();
        $this->showWatchList($watchList);
    }

    private function showWatchList(WatchList $watchList): void
    {
        $watching = $watchList->isWatchingForEverything() ? '*.*' : implode(', ', $watchList->paths());
        $this->comment('watching: ' . $watching);

        if ($watchList->hasIgnoring()) {
            $this->comment('ignoring: ' . implode(', ', $watchList->ignore()));
        }
    }

    private function title(): void
    {
        $this->comment(ConsoleApplication::NAME);
    }

    private function comment(string $text): void
    {
        $text = sprintf('<comment>%s</comment>', $this->message($text));
        $this->output->writeln($text);
    }

    private function info(string $text): void
    {
        $text = sprintf('<info>%s</info>', $this->message($text));
        $this->output->writeln($text);
    }

    public function start(string $command): void
    {
        $command = str_replace('exec', '', $command);
        $this->info(sprintf('starting `%s`', trim($command)));
    }

    public function restarting(string $command = null): void
    {
        $this->spinner->erase();
        $this->output->writeln('');
        $this->info('restarting due to changes...');

        if ($command !== null) {
            $this->start($command);
        }
    }

    public function subscribeToProcessOutput(Process $process): void
    {
        $process->stdout->on('data', static function ($data) {
            echo $data;
        });
    }

    public function showSpinner(LoopInterface $loop): void
    {
        $loop->addPeriodicTimer($this->spinner->interval(), function () {
            $this->spinner->spin();
        });
    }

    private function message(string $text): string
    {
        return sprintf('[%s] %s', ConsoleApplication::VERSION, $text);
    }
}
