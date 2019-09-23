<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use seregazhuk\PhpWatcher\Config\WatchList;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Screen
{
    private $output;

    private $appName;

    private $appVersion;

    public function __construct(OutputInterface $output, InputInterface $input, Application $application)
    {
        $this->output = new SymfonyStyle($input, $output);
        $this->appName = $application->getName();
        $this->appVersion = $application->getVersion();
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
        $this->comment($this->appVersion);
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
        $this->info(sprintf('starting `%s`', str_replace("'", '', $command)));
        $this->output->writeln('');
    }

    public function restarting(string $command): void
    {
        $this->output->writeln('');
        $this->info('restarting due to changes...');
        $this->start($command);
    }

    private function message(string $text): string
    {
        return sprintf('[%s] %s', $this->appName, $text);
    }
}
