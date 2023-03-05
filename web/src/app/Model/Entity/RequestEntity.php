<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\RequestRepository;
use Alfred\App\Model\Tables;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * class Request
 *
 * @package Alfred\App\Model\Entity
 */
#[Entity(repositoryClass: RequestRepository::class)]
#[Table(name: Tables::REQUEST_TABLE)]
class RequestEntity
{

    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[ManyToOne(targetEntity: ChatEntity::class, cascade: ['persist'], inversedBy: 'requests')]
    public ChatEntity $chat;

    #[ManyToOne(targetEntity: EventEntity::class)]
    public ?EventEntity $event;

    #[ManyToOne(targetEntity: WordEntity::class, cascade: ['persist'], inversedBy: 'requests')]
    public WordEntity $word;

    #[Column(type: Types::INTEGER, nullable: false)]
    public int $priority;

    #[Column(name: 'isActive', type: Types::BOOLEAN, nullable: false)]
    public bool $isActive;

    #[Column(name: 'isExplicit', type: Types::BOOLEAN, nullable: false)]
    public bool $isExplicit;

    #[Column(name: 'aggressiveness', type: Types::INTEGER, nullable: false)]
    public int $aggressiveness;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    #[ManyToMany(targetEntity: ResponseEntity::class, mappedBy: 'requests')]
    #[JoinTable(name: Tables::REQUESTS_RESPONSES_TABLE)]
    public Collection $responses;

    #[OneToMany(mappedBy: 'request', targetEntity: ResponseHistoryEntity::class)]
    public Collection $historyResponses;

    public function __construct()
    {
        $this->createdAt = new DateTime();

        $this->responses = new ArrayCollection();

        $this->historyResponses = new ArrayCollection();
    }

    public function addResponse(ResponseEntity $responseEntity) : void
    {
        $this->responses->add($responseEntity);
        $responseEntity->requests->add($this);
    }

    public function removeResponse(ResponseEntity $responseEntity) : void
    {
        $this->responses->removeElement($responseEntity);
        $responseEntity->requests->removeElement($this);
    }

}
