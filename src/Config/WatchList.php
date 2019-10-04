<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class WatchList
{
    private const DEFAULT_EXTENSIONS = ['php'];

    /**
     * @var string[]
     */
    private $paths;

    /**
     * @var string[]
     */
    private $extensions;

    /**
     * @var string[]
     */
    private $ignore;

    public function __construct(array $paths = [], array $extensions = [], array $ignore = [])
    {
        $this->paths = empty($paths) ? [getcwd()] : $paths;
        $this->extensions = empty($extensions) ? self::DEFAULT_EXTENSIONS : $extensions;
        $this->ignore = $ignore;
    }

    public function fileExtensions(): array
    {
        return array_map(
            function ($extension) {
                return '*.'.$extension;
            },
            $this->extensions
        );
    }

    /**
     * @return string[]
     */
    public function paths(): array
    {
        return $this->paths;
    }

    public function isWatchingForEverything(): bool
    {
        return empty($this->paths);
    }

    public function hasIgnoring(): bool
    {
        return !empty($this->ignore);
    }

    public function ignore(): array
    {
        return $this->ignore;
    }
}
