<?php

namespace Frosas\MiscBundle\Monolog\Handler;

use Frosas\Collection;
use Monolog\Handler\MailHandler;

class SwiftMailer extends MailHandler
{
    private $mailer;
    private $from;
    private $to;

    function __construct(\Swift_Mailer $mailer, $to, $from = null)
    {
        parent::__construct();
        $this->mailer = $mailer;
        $this->from = $from ?: $to;
        $this->to = $to;
    }

    protected function send($content, array $records)
    {
        $record = $this->getWorstRecord($records);
        $subject = $record['message'];

        // Don't wrap (Gmail sets white-space to pre-wrap)
        $body = '<pre style="white-space: pre !important">' . htmlspecialchars($content) . '</pre>';

        $message = new \Swift_Message;
        $message->setFrom($this->from);
        $message->setTo($this->to);
        $message->setSubject($subject);
        $message->setBody($body, 'text/html');
        $this->mailer->send($message);
    }

    private function getWorstRecord(array $records)
    {
        return Collection::wrap($records)
            ->sort(array(
                'getValue' => function($record) { return -$record['level']; },
                'sorting' => SORT_NUMERIC
            ))
            ->first();
    }
}