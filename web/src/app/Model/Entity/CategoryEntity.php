<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\CategoryRepository;
use Alfred\App\Model\Tables;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;

/**
 * class CategoryEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::CATEGORY_TABLE)]
#[Entity(repositoryClass: CategoryRepository::class)]
#[HasLifecycleCallbacks]
class CategoryEntity
{
    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[ManyToOne(targetEntity: CategoryEntity::class)]
    public ?CategoryEntity $parent;

    #[Column(name: 'name', length: 512, nullable: false)]
    public string $name;

    #[Column(name: 'isActive', type: Types::BOOLEAN, nullable: false)]
    public bool $isActive;


    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    #[ManyToMany(targetEntity: WordEntity::class, inversedBy: 'categories', )]
    #[JoinTable(name: Tables::WORDS_CATEGORIES_TABLE)]
    public Collection $words;

    public function __construct()
    {
        $this->createdAt = new DateTime();

        $this->words = new ArrayCollection();
    }

    #[PreUpdate()]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function addWord(WordEntity $wordEntity)
    {
        $this->words->add($wordEntity);
        $wordEntity->categories->add($this);
    }

    public function removeWord(WordEntity $wordEntity)
    {
        $this->words->removeElement($wordEntity);
        $wordEntity->categories->removeElement($this);
    }
}
