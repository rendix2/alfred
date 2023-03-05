<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\ChatRepository;
use Alfred\App\Model\Repository\EventRepository;
use Alfred\App\Model\Tables;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * class ChatEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::CHAT_TABLE)]
#[Entity(repositoryClass: ChatRepository::class)]
class ChatEntity
{
    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[Column(name: 'name', length: 512, nullable: false)]
    public string $name;

    #[Column(name: 'telegramId', nullable: false)]
    public int $telegramId;

    #[Column(name: 'isActive', type: Types::BOOLEAN, nullable: false)]
    public bool $isActive;

    /**
     * @var RequestEntity[] $requests
     */
    #[OneToMany(mappedBy: 'chat', targetEntity: RequestEntity::class, cascade: ['persist'])]
    public Collection $requests;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
    }

    public function removeWord(WordEntity $wordEntity)
    {
        foreach ($this->requests as $request) {
            if ($request->word->id === $wordEntity->id) {
                $this->requests->remove($request->id);
            }
        }
    }

}
