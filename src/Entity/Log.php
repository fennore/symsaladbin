<?php

namespace App\Entity;

use \DateTime;
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
    protected $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $message;

    /**
     * @ORM\Column(type="array")
     */
    protected $context;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $channel;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     * @Assert\NotBlank()
     */
    protected $level;

    /**
     * @ORM\Column(type="array")
     */
    protected $extra;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $created;

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

    public function getId() {
        return $this->id;
    }
}
