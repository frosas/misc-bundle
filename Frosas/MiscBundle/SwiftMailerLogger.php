<?php

namespace Frosas\MiscBundle;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class SwiftMailerLogger implements \Swift_Plugins_Logger
{
    private $logger;

    function __construct(LoggerInterface $logger, \Swift_Transport $transport)
    {
        $this->logger = $logger;

        // We register the plugin here and not using the swiftmailer.plugin tag
        // to avoid the ServiceCircularReferenceException we get when Monolog is
        // configured to mail the messages through SwiftMailer.
        $transport->registerPlugin(new \Swift_Plugins_LoggerPlugin($this));
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
