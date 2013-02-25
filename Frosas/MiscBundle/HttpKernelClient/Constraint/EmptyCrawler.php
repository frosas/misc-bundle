<?php

namespace Frosas\MiscBundle\HttpKernelClient\Constraint;

use Frosas\MiscBundle\HttpKernelClient\CrawlerHelper;

class EmptyCrawler extends \PHPUnit_Framework_Constraint
{
    function matches($helper)
    {
        return ! count($helper->getCrawler());
    }

    function toString()
    {
        return 'is empty';
    }

    function additionalFailureDescription($helper)
    {
        return "\nDump at " . $helper->dumpToFile();
    }
}