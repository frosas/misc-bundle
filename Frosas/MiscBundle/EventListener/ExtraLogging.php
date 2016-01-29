<?php

namespace Frosas\MiscBundle\EventListener;

use Frosas\Backtrace;
use Frosas\Collection;
use Frosas\String;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Yaml\Yaml;

/**
 * Logs requests and exceptions details
 */
class ExtraLogging
{
    private $logger;

    function __construct($logger)
    {
        $this->logger = $logger;
    }

    function onKernelRequest(GetResponseEvent $event)
    {
        // Don't log sub-requests as these has the same information
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) return;

        $this->logger->debug("Request URI: {$event->getRequest()->getUri()}");
        $this->logger->debug("Request headers:\n" . $this->dictionaryToString($event->getRequest()->headers));
        if (count($get = $event->getRequest()->query)) {
            $this->logger->debug("Request GET parameters:\n" . $this->dictionaryToString($get));
        }
        if (count($post = $event->getRequest()->request)) {
            $this->logger->debug("Request POST parameters:\n" . $this->dictionaryToString($post));
        }
        if (count($attributes = $event->getRequest()->attributes)) {
            $this->logger->debug("Request attributes:\n" . $this->dictionaryToString($attributes));
        }
        if (count($cookies = $event->getRequest()->cookies)) {
            $this->logger->debug("Request cookies:\n" . $this->dictionaryToString($cookies));
        }
        $this->logger->debug("Request server:\n" . $this->dictionaryToString($event->getRequest()->server));
    }

    function onKernelException(GetResponseForExceptionEvent $event)
    {
        $backtrace = Backtrace::createFromException($event->getException());
        $this->logger->debug("Exception trace:\n" . String::indent($backtrace));
    }

    private function dictionaryToString($dictionary)
    {
        return String::indent(Yaml::dump(Collection::toArray($dictionary), 1));
    }
}
