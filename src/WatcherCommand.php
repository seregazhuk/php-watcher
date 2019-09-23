<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use seregazhuk\PhpWatcher\Config\Builder;
use seregazhuk\PhpWatcher\Watcher\Factory as WatcherFactory;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class WatcherCommand extends BaseCommand
{
    private const DEFAULT_EXTENSIONS = 'php';
    private const DEFAULT_PHP_EXECUTABLE = 'php';
    private const DEFAULT_DELAY_IN_SECONDS = 1;

    protected function configure(): void
    {
        $this->setName('watch')
            ->setDescription('Restart PHP application once the source code changes.')
            ->addArgument('script', InputArgument::OPTIONAL, 'PHP script to run')
            ->addOption('watch', '-w', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Paths to watch', [basename(getcwd())])
            ->addOption('ext', '-e', InputOption::VALUE_OPTIONAL, 'Extensions to watch', self::DEFAULT_EXTENSIONS)
            ->addOption('ignore', '-i', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Paths to ignore', [])
            ->addOption('exec', null, InputOption::VALUE_OPTIONAL, 'PHP executable', self::DEFAULT_PHP_EXECUTABLE)
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delaying restart', self::DEFAULT_DELAY_IN_SECONDS)
            ->addOption('arguments', null, InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Arguments for the script', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = (new Builder())->build($input);
        $screen = new Screen($output, $input, $this->getApplication());
        $watcher = WatcherFactory::create($config->watchList(), $screen);

        $screen->showOptions($config->watchList());
        $watcher->startWatching($config->scriptToRun());
    }
}
