<?php

namespace Frosas\MiscBundle\HttpKernelClient;
 
use Frosas\Collection;
use Frosas\Map;
use PHPUnit_Framework_Assert as BaseAssert;
use Frosas\Error;
use Symfony\Component\HttpKernel\Client;

class CrawlerHelper
{
    private $crawler;

    static function create($crawler)
    {
        return new static($crawler);
    }

    function __construct($crawler)
    {
        if ($crawler instanceof Client) $crawler = $crawler->getCrawler();
        $this->crawler = $crawler;
    }

    function dumpToFile()
    {
        $dump = $this->dumpToString($this->crawler);
        $file = sys_get_temp_dir() . '/' . uniqid() . '.html';
        if (! file_put_contents($file, $dump)) throw Error::createExceptionFromLast();
        return $file;
    }

    function dumpToString()
    {
        return implode("\n\n", Collection::map($this->crawler, function($node) {
            return $node->ownerDocument->saveHtml($node);
        }));
    }

    function assertNotEmpty($message = null)
    {
        BaseAssert::assertThat($this->crawler, BaseAssert::logicalNot(new Constraint\EmptyCrawler), $message);
        return $this;
    }

    function assertContains($text, $message = null)
    {
        BaseAssert::assertThat($this->crawler, new Constraint\CrawlerContains($text), $message);
        return $this;
    }

    function assertNotContains($text, $message = null)
    {
        BaseAssert::assertThat($this->crawler, BaseAssert::logicalNot(new Constraint\CrawlerContains($text)), $message);
        return $this;
    }

    function getActionable($text)
    {
        if (count($actionable = $this->crawler->selectLink($text))) return $actionable;
        if (count($actionable = $this->crawler->selectButton($text))) return $actionable;
        throw new \Exception("No actionable with text \"$text\" found (see {$this->dumpToFile()})");
    }
}