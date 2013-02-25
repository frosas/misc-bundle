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
        $this->emptyConstraint = new EmptyCrawler;
    }

    function matches($helper)
    {
        $crawler = $helper->getCrawler()->filter('html:contains("' . $this->text . '")');
        return ! $this->emptyConstraint->matches(new CrawlerHelper($crawler));
    }

    function toString()
    {
        return "contains \"$this->text\"";
    }

    function additionalFailureDescription($helper)
    {
        return $this->emptyConstraint->additionalFailureDescription($helper);
    }

    protected function failureDescription($helper)
    {
        // Avoid the "Allowed memory size ... exhausted" by PHPUnit_Util_Type::export() on a Client
        return "$helper {$this->toString()}";
    }
}