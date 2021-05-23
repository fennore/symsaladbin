<?php

namespace App\EventSubscriber;

use App\Handler\DbBatchHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class DbBatchSubscriber implements EventSubscriberInterface
{
    private $batchHandler;

    public function __construct(DbBatchHandler $batchHandler)
    {
        $this->batchHandler = $batchHandler;
    }

    public function onKernelTerminate(TerminateEvent $event)
    {
        $this->batchHandler->cleanUpBatch();
    }

    public static function getSubscribedEvents()
    {
        return [
//           'kernel.terminate' => 'onKernelTerminate',
        ];
    }
}
