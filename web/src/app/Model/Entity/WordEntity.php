<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\WordRepository;
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
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;

/**
 * class WordEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Entity(repositoryClass: WordRepository::class)]
#[Table(name: Tables::WORD_TABLE)]
#[HasLifecycleCallbacks]
class WordEntity
{
    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[Column(name: 'wordText', type: Types::STRING, length: 512, nullable: false)]
    public string $wordText;

    #[Column(name: 'description', type: Types::TEXT, nullable: true)]
    public ?string $description;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    #[ManyToMany(targetEntity: CategoryEntity::class, mappedBy: 'words', inversedBy: 'categories')]
    #[JoinTable(name: Tables::WORDS_CATEGORIES_TABLE)]
    public Collection $categories;

    #[OneToMany(mappedBy: 'word', targetEntity: WordVariantEntity::class)]
    public Collection $variants;

    /**
     * @var RequestEntity[] $requests
     */
    #[OneToMany(mappedBy: 'word', targetEntity: RequestEntity::class, cascade: ['persist'])]
    public Collection $requests;

    public function __construct()
    {
        $this->createdAt = new DateTime();

        $this->categories = new ArrayCollection();
        $this->variants = new ArrayCollection();

        $this->requests = new ArrayCollection();
    }

    #[PreUpdate()]
    public function preUpdate() : void
    {
        $this->updatedAt = new DateTime();
    }

    public function addCategory(CategoryEntity $categoryEntity) : void
    {
        $this->categories->add($categoryEntity);
        $categoryEntity->words->add($this);
    }

    public function removeCategory(CategoryEntity $categoryEntity) : void
    {
        $this->categories->removeElement($categoryEntity);
        $categoryEntity->words->removeElement($this);
    }

    public function addChat(ChatEntity $chatEntity) : void
    {
        $request = new RequestEntity();
        $request->chat = $chatEntity;
        $request->word = $this;
        $request->event = null;

        $this->requests->add($request);
    }

    public function removeChat(ChatEntity $chatEntity) : void
    {
        foreach ($this->requests as $request) {
            if ($request->chat->id === $chatEntity->id) {
                $this->requests->removeElement($request);
                $chatEntity->requests->removeElement($request);
                break;
            }
        }
    }
}
