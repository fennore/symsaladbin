<?php

namespace App\Handler;

use App\Entity\Log;
use App\Repository\LogRepository;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Handler for Monolog as service.
 * This handler writes logs using a doctrine repository.
 * Note: this should not log any generic doctrine logs that log writing to database,
 *       as this would result in an infinite cycle;
 *       hence the doctrine channel is excluded for this handler in logger config.
 */
class DbLogHandler extends AbstractProcessingHandler
{
    /**
     * @var LogRepository
     */
    protected $logRepo;

    /**
     * {@includeDocs}.
     */
    public function __construct(/*LogRepository $logRepo, */$level = Logger::DEBUG, $bubble = true)
    {
//        $this->logRepo = $logRepo;
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the given Monolog record to database.
     *
     * @param array $record Monolog record
     */
    public function write(array $record): void
    {
        $log = new Log($record);
//        $this->logRepo
//            ->createLog($log);
    }
}
