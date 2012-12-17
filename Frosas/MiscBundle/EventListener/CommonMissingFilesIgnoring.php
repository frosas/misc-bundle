<?php

namespace Frosas\MiscBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Frosas\Collection;

class CommonMissingFilesIgnoring
{
    private $ignoredFilesRegexes = array(
        '#^/robots.txt$#',
        '#^/favicon.ico$#',
        '#^/apple-touch-icon.*\.png$#'
    );

    function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->isAMissingIgnoredFile($event)) {
            // Avoid \Symfony\Component\HttpKernel\EventListener\ExceptionListener to log the exception.
            // Unfortunately we won't get the default error page.
            $event->setResponse(new Response('', 404));
        }
    }

    private function isAMissingIgnoredFile(GetResponseForExceptionEvent $event)
    {
        return
            $event->getException() instanceof NotFoundHttpException &&
            Collection::any($this->ignoredFilesRegexes, function($regex) use ($event) {
                return preg_match($regex, $event->getRequest()->getPathInfo());
            });
    }
}
