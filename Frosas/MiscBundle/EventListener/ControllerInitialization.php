<?php

namespace Frosas\MiscBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Frosas\MiscBundle\InitializableController;

class ControllerInitialization
{
    function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (! is_array($controller)) return; // Not an array($object, $method)
        list($controller, $method) = $controller;
        if ($controller instanceof InitializableController) $controller->initialize();
    }
}
