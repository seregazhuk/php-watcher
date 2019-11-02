<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use AlecRabbit\Snake\Contracts\SpinnerInterface;
use React\ChildProcess\Process;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\Builder;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener;
use seregazhuk\PhpWatcher\Screen\Screen;
use seregazhuk\PhpWatcher\Screen\SpinnerFactory;
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
            ->addArgument('script', InputArgument::REQUIRED, 'Script to run')
            ->addOption('watch', '-w', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Paths to watch')
            ->addOption('ext', '-e', InputOption::VALUE_OPTIONAL, 'Extensions to watch', '')
            ->addOption('ignore', '-i', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Paths to ignore', [])
            ->addOption('exec', null, InputOption::VALUE_OPTIONAL, 'PHP executable')
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delaying restart')
            ->addOption('signal', null, InputOption::VALUE_OPTIONAL, 'Signal to reload the app')
            ->addOption('arguments', null, InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Arguments for the script', [])
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Path to config file')
            ->addOption('no-spinner', null, InputOption::VALUE_OPTIONAL, 'Remove spinner from output', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create();
        $config = (new Builder())->build($input);
        $spinner = SpinnerFactory::create($output, $config->spinnerDisabled());

        $this->addTerminationListeners($loop, $spinner);

        $screen = new Screen(new SymfonyStyle($input, $output), $spinner);
        $filesystem = new ChangesListener($loop, $config->watchList());
        $watcher = new Watcher($loop, $screen, $filesystem);

        $screen->showOptions($config->watchList());
        $process = new Process($config->command());
        $watcher->startWatching($process, $config->signal(), $config->delay());
    }

    /**
     * When terminating the watcher we need to manually restore the cursor after the spinner.
     */
    private function addTerminationListeners(LoopInterface $loop, SpinnerInterface $spinner): void
    {
        $func = static function (int $signal) use ($spinner) {
            $spinner->end();
            exit($signal);
        };

        $loop->addSignal(SIGINT, $func);
        $loop->addSignal(SIGTERM, $func);
    }
}
