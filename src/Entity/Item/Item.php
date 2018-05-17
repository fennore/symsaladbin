<?php

namespace App\Entity\Item;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Tools\CleanPathString;

/**
 * @ORM\Entity
 * @ORM\Table(name="item",indexes={@ORM\Index(name="item_list_select", columns={"status", "created", "weight"})})
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
     * @ORM\Column(unique=true)
     *
     * @var type
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

    public function setTitle(string $title)
    {
        $this->title = $title;

        // Update path from title
        $this->setPath(new CleanPathString($title));
        // Flag updated
        $this->setUpdated();

        return $this;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
        // Flag updated
        $this->setUpdated();

        return $this;
    }

    public function setPath(CleanPathString $path)
    {
        $this->path = (string) $path;

        return $this;
    }

    /**
     * Link other Items to this Item.
     *
     * @param ArrayCollection $items a collection of Item entities
     *
     * @return $this
     */
    public function setLink(ArrayCollection $items)
    {
        $this->link = $items;

        return $this;
    }

    public function setWeight(int $weight)
    {
        $this->weight = $weight;

        return $this;
    }

    public function setActive()
    {
        $this->status = 1;

        return $this;
    }

    public function setInactive()
    {
        $this->status = 0;

        return $this;
    }

    /**
     * Flag this Item as updated.
     * This means setting the updated property to the current timestamp value.
     *
     * @return $this
     */
    public function setUpdated()
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
}
