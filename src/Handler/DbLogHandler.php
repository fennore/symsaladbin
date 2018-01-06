<?php

namespace App\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
//use App\Repository\LogRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Log;

/**
 * Handler for monolog as service.
 * This handler writes logs using a doctrine repository.
 * Note: this should not log any generic doctrine logs that log writing to database,
 *       as this would result in an infinite cycle;
 *       hence the doctrine channel is excluded for this handler in logger config.
 *
 * @todo
 *  Ideally we should just inject App\Repository\LogRepository using the constructor.
 *  Unfortunately this somehow results in a circular reference with dependency injection
 *  on the default entity manager of doctrine, producing an error.
 *  No fix has been found for this yet. Injecting the full service container is the work-around for now.
 *  Trying to inject the EntityManagerInterface instead
 *  resulted in fatal error due to memory usage (infinite loop?).
 */
class DbLogHandler extends AbstractProcessingHandler
{
    protected $logRepository;
    protected $container;

    /**
     * @param ContainerInterface $container
     * @param type               $level
     * @param type               $bubble
     */
    public function __construct(ContainerInterface $container, $level = Logger::DEBUG, $bubble = true)
    {
        $this->container = $container;
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the given Monolog record to database.
     *
     * @param array $record Monolog record
     */
    public function write(array $record)
    {
        $log = new Log($record);
        $this->container
            ->get('doctrine')
            ->getManager()
            ->getRepository(Log::class)
            ->createLog($log);
    }
}
