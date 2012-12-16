<?php

namespace Frosas\MiscBundle;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class SwiftMailerLogger implements \Swift_Plugins_Logger
{
    private $logger;

    function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    function add($entry)
    {
        $this->logger->debug(trim($entry));
    }

    function clear()
    {
    }

    function dump()
    {
    }
}
