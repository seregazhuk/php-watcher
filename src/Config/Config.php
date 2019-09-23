<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class Config
{
    /**
     * @var ScriptToRun
     */
    private $scriptToRun;

    /**
     * @var WatchList
     */
    private $watchList;

    public function __construct(ScriptToRun $scriptToRun, WatchList $watchList)
    {
        $this->scriptToRun = $scriptToRun;
        $this->watchList = $watchList;
    }

    public function watchList(): WatchList
    {
        return $this->watchList;
    }

    public function scriptToRun(): ScriptToRun
    {
        return $this->scriptToRun;
    }
}
