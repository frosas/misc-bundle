<?php

namespace Frosas\MiscBundle\EventListener;

use Frosas\Backtrace;
use Frosas\Collection;

/**
 * Avoids flush overlappings
 */
class NoEntityFlushOverlapping
{
    private $currentFlush;

    function preFlush()
    {
        if ($this->currentFlush) {
            throw new \RuntimeException(
                "There is already one flush being executed.\n\n" .
                "Backtrace of previous flush:\n{$this->currentFlush['backtrace']}");
        }

        $this->currentFlush = array('backtrace' => new Backtrace);
    }

    function postFlush()
    {
        if (! $this->currentFlush) {
            throw new \RuntimeException("postFlush triggered before a preFlush (possible bug in Doctrine)");
        }

        $this->currentFlush = null;
    }
}
