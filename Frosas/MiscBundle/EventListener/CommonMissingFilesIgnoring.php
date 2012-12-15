<?php

namespace Frosas\MiscBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommonMissingFilesIgnoring
{
    private $ignoredFiles = array(
        '/robots.txt',
        '/favicon.ico',
        '/apple-touch-icon-precomposed.png',
        '/apple-touch-icon.png'
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
            in_array($event->getRequest()->getPathInfo(), $this->ignoredFiles);
    }
}