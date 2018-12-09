<?php

namespace App\Entity\Item;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Tools\CleanPathString;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="item",
 *      indexes={@ORM\Index(name="item_list_select", columns={"status", "created", "weight"})},
 *      uniqueConstraints={@ORM\UniqueConstraint(name="unique_path", columns={"path", "type"})}
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
class Item
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * The Item type.
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="Item", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      name="itemlink",
     *      joinColumns={@ORM\JoinColumn(name="item_id_1", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="item_id_2", referencedColumnName="id")})
     *
     * @var Item Linked Item
     */
    protected $link;

    /**
     * @ORM\Column()
     *
     * @var string title of the item
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string text that can contain HTML
     */
    protected $content;

    /**
     * @ORM\Column()
     *
     * @var string
     */
    protected $path;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int date created
     */
    protected $created;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int date last modified
     */
    protected $updated;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int item weight for sorting
     */
    protected $weight;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     *
     * @var int Item status. Like open (1) / closed (0)
     */
    protected $status;

    public function __construct(string $title, string $content)
    {
        $this
            ->setTitle($title)
            ->setContent($content)
            ->setActive()
            ->setWeight(0)
            ->setLink(new ArrayCollection());
        $this->created = $this->updated;
    }

    /**
     * @param string $title
     *
     * @return \App\Entity\Item\Item
     */
    public function setTitle(string $title): Item
    {
        $this->title = $title;

        // Update path from title
        $this->setPath(new CleanPathString($title));
        // Flag updated
        $this->setUpdated();

        return $this;
    }

    /**
     * @param string $content
     *
     * @return \App\Entity\Item\Item
     */
    public function setContent(string $content): Item
    {
        $this->content = $content;
        // Flag updated
        $this->setUpdated();

        return $this;
    }

    /**
     * @param CleanPathString $path
     *
     * @return \App\Entity\Item\Item
     */
    public function setPath(CleanPathString $path): Item
    {
        $this->path = (string) $path;

        return $this;
    }

    /**
     * Link other Items to this Item.
     *
     * @param ArrayCollection $items a collection of Item entities
     *
     * @return \App\Entity\Item\Item
     */
    public function setLink(ArrayCollection $items): Item
    {
        $this->link = $items;

        return $this;
    }

    /**
     * @param int $weight
     *
     * @return \App\Entity\Item\Item
     */
    public function setWeight(int $weight): Item
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return \App\Entity\Item\Item
     */
    public function setActive(): Item
    {
        $this->status = 1;

        return $this;
    }

    /**
     * @return \App\Entity\Item\Item
     */
    public function setInactive(): Item
    {
        $this->status = 0;

        return $this;
    }

    /**
     * Flag this Item as updated.
     * This means setting the updated property to the current timestamp value.
     *
     * @return \App\Entity\Item\Item
     */
    public function setUpdated(): Item
    {
        $currentDT = new DateTime();
        $this->updated = $currentDT->getTimestamp();

        return $this;
    }

//    public function setType() {
//        $this->type = strtolower((new \ReflectionClass($this))->getShortName());
//
//        return $this;
//    }

    public function getTitle(): string
    {
        return $this - title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $format Date format to return the created date in.
     *                       Defaults to Y-m-d H:i:s
     */
    public function getCreated(string $format = 'Y-m-d H:i:s'): string
    {
        return DateTime::createFromFormat('U', $this->created)->format($format);
    }

    /**
     * @param string $format Date format to return the updated date in.
     *                       Defaults to Y-m-d H:i:s
     */
    public function getUpdated(string $format = 'Y-m-d H:i:s'): string
    {
        return DateTime::createFromFormat('U', $this->updated)->format($format);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
