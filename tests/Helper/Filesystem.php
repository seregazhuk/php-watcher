<?php declare(strict_types=1);

namespace tests\Helper;

use Symfony\Component\Yaml\Yaml;

final class Filesystem
{
    private const FIXTURES_DIR = 'tests/fixtures/';

    private static $files = [];

    public static function createFile(string $name, string $contents): void
    {
        self::$files[] = $name;
        file_put_contents($name, $contents);
    }

    public static function createHelloWorldPHPFile(): string
    {
        $name = self::FIXTURES_DIR . 'test.php';
        self::createFile($name, '<?php echo "Hello, world";');

        return $name;
    }

    public static function createHelloWorldPHPFileWithSignalsHandling(): string
    {
        $name = self::FIXTURES_DIR . 'test.php';
        $code = <<<CODE
<?php
declare(ticks = 1);

pcntl_signal(SIGTERM, "handler");

while (true) {
    echo "Hello, world";
    sleep(1);
}
function handler() {
    echo 'SIGTERM was received' . PHP_EOL;
    exit;
}
CODE;
        self::createFile($name, $code);

        return $name;
    }


    public static function createConfigFile(array $options): string
    {
        $name = self::FIXTURES_DIR . '.php-watcher.yml';
        self::createFile($name, Yaml::dump($options));

        return $name;
    }

    public static function changeFileContentsWith(string $file, string $contents): void
    {
        file_put_contents($file, $contents);
    }

    public static function appendFileContentsWith(string $file, string $contents): void
    {
        $previousContents = file_get_contents($file);
        file_put_contents($file, $previousContents. $contents);
    }

    public static function fixturesDir(): string
    {
        return str_replace('tests/', '', self::FIXTURES_DIR);
    }

    public static function clear(): void
    {
        foreach (self::$files as $file) {
            @unlink($file);
        }

        self::$files = [];
    }
}
