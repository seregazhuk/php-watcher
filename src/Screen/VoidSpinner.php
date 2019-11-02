<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Screen;

use AlecRabbit\Snake\Contracts\SpinnerInterface;

final class VoidSpinner implements SpinnerInterface
{
    public function spin(): void
    {
    }

    public function interval(): float
    {
        return 1.0;
    }

    public function begin(): void
    {
    }

    public function end(): void
    {
    }

    public function erase(): void
    {
    }

    public function useStdOut(): void
    {
    }
}
