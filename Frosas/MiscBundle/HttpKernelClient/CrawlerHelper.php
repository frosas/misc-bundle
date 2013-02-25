<?php

namespace Frosas\MiscBundle\HttpKernelClient;
 
use Frosas\Collection;
use Frosas\Map;
use Frosas\String;
use PHPUnit_Framework_Assert as BaseAssert;
use Frosas\Error;
use Symfony\Component\BrowserKit\History;
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
        $this->crawler = $crawler;
    }

    function getCrawler()
    {
        if ($this->crawler instanceof Client) return $this->crawler->getCrawler();

        return $this->crawler;
    }

    function dumpToFile()
    {
        $dump = $this->dumpToString($this->crawler);
        $extension = $this->crawler instanceof Client ? 'txt' : 'html';
        $file = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
        if (! file_put_contents($file, $dump)) throw Error::createExceptionFromLast();
        return $file;
    }

    function dumpToString()
    {
        if ($this->crawler instanceof Client) {
            // TODO Output headers as a HTML comment
            $string = '';
            foreach ($this->getHistoryRequests() as $request) {
                $string .= $request;
            }
            if ($response = $this->crawler->getResponse()) $string .= $response;
            return $string;
        }

        return implode("\n\n", Collection::map($this->crawler, function($node) {
            return $node->ownerDocument->saveHtml($node);
        }));
    }

    function assertNotEmpty($message = null)
    {
        BaseAssert::assertThat($this, BaseAssert::logicalNot(new Constraint\EmptyCrawler), $message);
        return $this;
    }

    function assertContains($text, $message = null)
    {
        BaseAssert::assertThat($this, new Constraint\CrawlerContains($text), $message);
        return $this;
    }

    function assertNotContains($text, $message = null)
    {
        BaseAssert::assertThat($this, BaseAssert::logicalNot(new Constraint\CrawlerContains($text)), $message);
        return $this;
    }

    function getActionable($text)
    {
        if (count($actionable = $this->getCrawler()->selectLink($text))) return $actionable;
        if (count($actionable = $this->getCrawler()->selectButton($text))) return $actionable;
        throw new \Exception("No actionable with text \"$text\" found (see {$this->dumpToFile()})");
    }

    function __toString()
    {
        return get_class($this);
    }

    private function getHistoryRequests()
    {
        $history = $this->crawler->getHistory();
        $stack = new \ReflectionProperty($history, 'stack');
        $stack->setAccessible(true);
        $requests = $stack->getValue($history);

        $filterRequest = new \ReflectionMethod($this->crawler, 'filterRequest');
        $filterRequest->setAccessible(true);
        $crawler = $this->crawler;
        return Collection::map($requests, function($request) use ($filterRequest, $crawler) {
            return $filterRequest->invoke($crawler, $request);
        });
    }
}