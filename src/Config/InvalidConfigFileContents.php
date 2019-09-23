<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

use RuntimeException;

final class InvalidConfigFileContents extends RuntimeException
{
    public static function invalidContents(string $path): self
    {
        return new self("The content of configfile `{$path}` is not valid. Make sure this file contains valid yaml.");
    }
}
