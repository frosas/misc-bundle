<?php

namespace Frosas\MiscBundle\HttpKernelClient\Constraint;

use Frosas\MiscBundle\HttpKernelClient\CrawlerHelper;

class EmptyCrawler extends \PHPUnit_Framework_Constraint
{
    function matches($crawler)
    {
        return ! count($crawler);
    }

    function toString()
    {
        return 'is empty';
    }

    function additionalFailureDescription($crawler)
    {
        return "\nDump at " . CrawlerHelper::create($crawler)->dumpToFile();
    }
}