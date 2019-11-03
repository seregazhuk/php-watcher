<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Screen;

use AlecRabbit\Snake\Contracts\SpinnerInterface;
use AlecRabbit\Snake\Spinner;
use Symfony\Component\Console\Output\OutputInterface;

final class SpinnerFactory
{
    public static function create(OutputInterface $output, bool $spinnerDisabled): SpinnerInterface
    {
        $hasColorSupport = $output->getFormatter()->isDecorated();
        if (!$hasColorSupport || $spinnerDisabled) {
            return new VoidSpinner();
        }

        return new Spinner();
    }
}
