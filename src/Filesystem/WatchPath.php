<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem;

final class WatchPath
{
    private $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function isFileOrPattern(): bool
    {
        return !$this->isDirectory() || !file_exists($this->pattern);
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
