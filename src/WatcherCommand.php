<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use React\ChildProcess\Process;
use React\EventLoop\Factory;
use seregazhuk\PhpWatcher\Config\Builder;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener;
use seregazhuk\PhpWatcher\Watcher\Watcher;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class WatcherCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('watch')
            ->setDescription('Restart PHP application once the source code changes.')
            ->addArgument('script', InputArgument::OPTIONAL, 'PHP script to run')
            ->addOption('watch', '-w', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Paths to watch')
            ->addOption('ext', '-e', InputOption::VALUE_OPTIONAL, 'Extensions to watch', '')
            ->addOption('ignore', '-i', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Paths to ignore', [])
            ->addOption('exec', null, InputOption::VALUE_OPTIONAL, 'PHP executable')
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delaying restart')
            ->addOption('arguments', null, InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Arguments for the script', [])
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Path to config file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = (new Builder())->build($input);
        $loop = Factory::create();

        $screen = new Screen(new SymfonyStyle($input, $output), $this->getApplication());
        $filesystem = new ChangesListener($loop, $config->watchList());
        $watcher = new Watcher($loop, $screen, $filesystem);

        $screen->showOptions($config->watchList());
        $process = new Process($config->command());
        $watcher->startWatching($process, $config->delay());
    }
}
