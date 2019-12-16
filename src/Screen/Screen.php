<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Screen;

use AlecRabbit\Snake\Contracts\SpinnerInterface;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\ConsoleApplication;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Screen
{
    private $output;

    private $spinner;

    public function __construct(SymfonyStyle $output, SpinnerInterface $spinner)
    {
        $this->output = $output;
        $this->spinner = $spinner;
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
        $this->comment(ConsoleApplication::VERSION);
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

    private function warning(string $text): void
    {
        $text = sprintf('<fg=red>%s</>', $this->message($text));
        $this->output->writeln($text);
    }

    public function start(string $command): void
    {
        $command = str_replace(['exec', PHP_BINARY], ['', 'php'], $command);
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

    public function processExit(int $exitCode): void
    {
        if ($exitCode === 0) {
            $this->info('clean exit - waiting for changes before restart');
        } else {
            $this->warning('app crashed - waiting for file changes before starting...');
        }
    }

    public function plainOutput(string $data): void
    {
        $this->output->write($data);
    }

    public function showSpinner(LoopInterface $loop): void
    {
        $this->spinner->begin();
        $loop->addPeriodicTimer($this->spinner->interval(), function () {
            $this->spinner->spin();
        });
    }

    private function message(string $text): string
    {
        return sprintf('[%s] %s', ConsoleApplication::NAME, $text);
    }
}
