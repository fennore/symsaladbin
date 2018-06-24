<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * Json is safer because of no serialisation, and cleaner.
     *
     * @ORM\Column(type="json")
     */
    private $context;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $channel;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     * @Assert\NotBlank()
     */
    private $level;

    /**
     * Json is safer because of no serialisation, and cleaner.
     *
     * @ORM\Column(type="json")
     */
    private $extra;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $created;

    /**
     * @param array $record Monolog logging record
     */
    public function __construct($record)
    {
        $datetime = new DateTime();
        $this->message = $record['message'];
        $this->channel = $record['channel'];
        $this->level = $record['level'];
        $this->context = $record['context'];
        $this->extra = $record['extra'];
        $this->created = $datetime->getTimestamp();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
