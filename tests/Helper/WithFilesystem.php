<?php declare(strict_types=1);

namespace tests\Helper;

trait WithFilesystem
{
    protected function tearDown(): void
    {
        Filesystem::clear();
        parent::tearDown();
    }
}
