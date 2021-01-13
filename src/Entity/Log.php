<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'log')]
#[ORM\Entity(repositoryClass:'App\Repository\LogRepository')]
class Log implements EntityInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private int $id;

    #[ORM\Column(type:'text')]
    #[Assert\NotBlank]
    private string $message;

    #[ORM\Column(type:'json')]
    private stdClass $context;

    #[ORM\Column(type:'string')]
    #[Assert\NotBlank]
    private string $channel;

    #[ORM\Column(type:'smallint', options:['unsigned' => true])]
    #[Assert\NotBlank]
    private int $level;

    #[ORM\Column(type:'json')]
    private stdClass $extra;

    #[ORM\Column(type:'integer', options:['unsigned' => true])]
    private int $created;

    /** @param array $record Monolog logging record */
    public function __construct(array $record)
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
