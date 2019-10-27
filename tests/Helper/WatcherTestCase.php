<?php declare(strict_types=1);

namespace tests\Helper;

use PHPUnit\Framework\TestCase;

abstract class WatcherTestCase extends TestCase
{
    protected function tearDown(): void
    {
        Filesystem::clear();
        parent::tearDown();
    }

    protected function wait(): void
    {
        usleep(2000000);
    }
}
