<?php

namespace Frosas\MiscBundle\HttpKernelClient\Constraint;

use Frosas\MiscBundle\HttpKernelClient\CrawlerHelper;

class CrawlerContains extends \PHPUnit_Framework_Constraint
{
    private $text;
    private $emptyConstraint;

    function __construct($text)
    {
        $this->text = $text;
    }

    function matches($crawler)
    {
        $crawler = $crawler->filter('html:contains("' . $this->text . '")');
        $this->emptyConstraint = new EmptyCrawler;
        return ! $this->emptyConstraint->matches($crawler);
    }

    function toString()
    {
        return "contains \"$this->text\"";
    }

    function additionalFailureDescription($crawler)
    {
        return $this->emptyConstraint->additionalFailureDescription($crawler);
    }
}