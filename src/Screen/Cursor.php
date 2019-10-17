<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Screen;

final class Cursor
{
    public function startOfLine(): void
    {
        echo "\033[1D";
    }

    public function erase(): void
    {
        echo "\033[1K";
    }

    public function write(int $foregroundColor, string $text): void
    {
        echo "\e[38;5;{$foregroundColor}m{$text}\e[0m";
    }

    public function hide(): void
    {
        echo "\033[?25l";
    }
}
