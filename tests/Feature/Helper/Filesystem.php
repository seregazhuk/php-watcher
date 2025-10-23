<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature\Helper;

use Symfony\Component\Yaml\Yaml;

final class Filesystem
{
    private const FIXTURES_DIR = 'tests/fixtures/';

    public static function createFile(string $name, string $contents): void
    {
        file_put_contents($name, $contents);
    }

    public static function createHelloWorldPHPFile(): string
    {
        $name = self::buildFilePath('test.php');
        self::createFile($name, '<?php echo "Hello, world";');

        return $name;
    }

    public static function createPHPFileThatCrashes(): string
    {
        $name = self::buildFilePath('test.php');
        self::createFile($name, '<?php exit(1);');

        return $name;
    }

    public static function createStdErrorPHPFile(): string
    {
        $name = self::buildFilePath('test.php');
        self::createFile($name, '<?php fwrite(STDERR, "Some error");');

        return $name;
    }

    public static function createHelloWorldPHPFileWithSignalsHandling(): string
    {
        $name = self::buildFilePath('test_signals.php');
        $code = <<<'CODE'
<?php declare(ticks = 1);

pcntl_signal(SIGTERM, "handler");
pcntl_signal(SIGINT, "handler");

while (true) {
    echo "Hello, world";
    sleep(1);
}
function handler($signal) {
    echo "$signal signal was received" . PHP_EOL;
    exit;
}
CODE;
        self::createFile($name, $code);

        return $name;
    }

    /**
     * @param  array{watch: array<string>}  $options
     */
    public static function createConfigFile(array $options): string
    {
        $name = self::buildFilePath('.php-watcher.yml');
        self::createFile($name, Yaml::dump($options));

        return $name;
    }

    public static function changeFileContentsWith(string $file, string $contents): void
    {
        file_put_contents($file, $contents);
    }

    public static function fixturesDir(): string
    {
        return self::FIXTURES_DIR;
    }

    private static function buildFilePath(string $filename): string
    {
        return self::FIXTURES_DIR.$filename;
    }

    public static function clear(): void
    {
        $files = glob(self::FIXTURES_DIR.'/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}
