<?php declare(strict_types=1);

namespace tests\Feature\Helper;

trait WithFilesystem
{
    protected function tearDown(): void
    {
        Filesystem::clear();
        parent::tearDown();
    }
}
