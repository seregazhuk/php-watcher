<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem;

final class WatchPath
{
    public function __construct(private readonly string $pattern) {}

    public function isFileOrPattern(): bool
    {
        if (! $this->isDirectory()) {
            return true;
        }

        return ! file_exists($this->pattern);
    }

    private function directoryPart(): string
    {
        return pathinfo($this->pattern, PATHINFO_DIRNAME);
    }

    public function fileName(): string
    {
        return pathinfo($this->pattern, PATHINFO_BASENAME);
    }

    private function isDirectory(): bool
    {
        return is_dir($this->pattern);
    }

    public function path(): string
    {
        return $this->isDirectory() ? $this->pattern : $this->directoryPart();
    }
}
