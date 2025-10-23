<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class WatchList
{
    private const DEFAULT_EXTENSIONS = ['php'];

    /**
     * @var string[]
     */
    private readonly array $paths;

    /**
     * @var string[]
     */
    private readonly array $extensions;

    /**
     * @param  string[]  $paths
     * @param  string[]  $extensions
     */
    public function __construct(
        array $paths = [],
        array $extensions = [],
        /**
         * @var string[] $ignore
         */
        private readonly array $ignore = []
    ) {
        $this->paths = $paths === [] ? [getcwd()] : $paths;
        $this->extensions = $extensions === [] ? self::DEFAULT_EXTENSIONS : $extensions;
    }

    /**
     * @return string[]
     */
    public function getFileExtensions(): array
    {
        return array_map(
            fn (string $extension): string => '.'.$extension,
            $this->extensions
        );
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    public function isWatchingForEverything(): bool
    {
        return $this->paths === [];
    }

    public function hasIgnoring(): bool
    {
        return $this->ignore !== [];
    }

    /**
     * @return string[]
     */
    public function getIgnored(): array
    {
        return $this->ignore;
    }

    public function merge(self $another): self
    {
        return new self(
            $this->hasDefaultPath() && $another->paths !== [] ? $another->paths : $this->paths,
            $this->hasDefaultExtensions() && $another->extensions !== [] ? $another->extensions : $this->extensions,
            $this->ignore === [] && $another->ignore !== [] ? $another->ignore : $this->ignore
        );
    }

    private function hasDefaultPath(): bool
    {
        return $this->paths === [getcwd()];
    }

    private function hasDefaultExtensions(): bool
    {
        return $this->extensions === self::DEFAULT_EXTENSIONS;
    }
}
