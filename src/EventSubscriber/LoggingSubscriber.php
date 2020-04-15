<?php

namespace App\EventSubscriber;

use App\Repository\LogRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LoggingSubscriber implements EventSubscriberInterface
{
    private $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $this->logRepository->emptyLog();
    }

    public static function getSubscribedEvents()
    {
        return [
           'kernel.request' => 'onKernelRequest',
        ];
    }
}
