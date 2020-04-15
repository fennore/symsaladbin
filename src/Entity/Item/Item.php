<?php

namespace App\Entity\Item;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Tools\CleanPathString;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="item",
 *      indexes={@ORM\Index(name="item_list_select", columns={"status", "created", "weight"})},
 *      uniqueConstraints={@ORM\UniqueConstraint(name="unique_path", columns={"path", "type"})}
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @Serializer\Discriminator(field = "type", disabled = true, map = {"item" = "Item"})
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
    protected int $id;

    /**
     * @Serializer\Exclude
     * 
     * The Item type.
     *
     * @var string
     */
    protected string $type;

    /**
     * @Serializer\Type("ArrayCollection")
     * @ORM\ManyToMany(targetEntity="Item", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      name="itemlink",
     *      joinColumns={@ORM\JoinColumn(name="item_id_1", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="item_id_2", referencedColumnName="id")})
     *
     * @var ArrayCollection Linked Item
     */
    protected ArrayCollection $link;

    /**
     * @ORM\Column()
     *
     * @var string title of the item
     */
    protected string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string text that can contain HTML
     */
    protected string $content;

    /**
     * @ORM\Column()
     *
     * @var string
     */
    protected string $path;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int date created
     */
    protected int $created;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int date last modified
     */
    protected int $updated;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int item weight for sorting
     */
    protected int $weight;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     *
     * @var int Item status. Like open (1) / closed (0)
     */
    protected int $status;

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
     * @return static
     */
    public function setTitle(string $title): self
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
     * @return static
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        // Flag updated
        $this->setUpdated();

        return $this;
    }

    /**
     * @param CleanPathString $path
     *
     * @return static
     */
    public function setPath(CleanPathString $path): self
    {
        $this->path = (string) $path;

        return $this;
    }

    /**
     * Link other Items to this Item.
     *
     * @param ArrayCollection $items a collection of Item entities
     *
     * @return static
     */
    public function setLink(ArrayCollection $items): self
    {
        $this->link = $items;

        return $this;
    }

    /**
     * @param self $item
     */
    public function addLink(Item $item) {
        $this->link->add($item);
    }

    /**
     * @param int $weight
     *
     * @return static
     */
    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return static
     */
    public function setActive(): self
    {
        $this->status = 1;

        return $this;
    }

    /**
     * @return static
     */
    public function setInactive(): self
    {
        $this->status = 0;

        return $this;
    }

    /**
     * Flag this Item as updated.
     * This means setting the updated property to the current timestamp value.
     *
     * @return static
     */
    public function setUpdated(): self
    {
        $currentDT = new DateTime();
        $this->updated = $currentDT->getTimestamp();

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): ?string
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
