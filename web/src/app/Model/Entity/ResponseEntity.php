<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\ResponseRepository;
use Alfred\App\Model\Tables;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\Inflector\Rules\Word;
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
use Nette\Http\Response;

/**
 * class ResponseEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::RESPONSE_TABLE)]
#[Entity(repositoryClass: ResponseRepository::class)]
#[HasLifecycleCallbacks]
class ResponseEntity
{

    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[ManyToOne(targetEntity: AnswerEntity::class)]
    public ?AnswerEntity $answer;

    #[ManyToOne(targetEntity: PollEntity::class)]
    public ?PollEntity $poll;

    #[ManyToOne(targetEntity: GifEntity::class)]
    public ?GifEntity $gif;

    #[ManyToOne(targetEntity: LocationEntity::class)]
    public ?LocationEntity $location;

    #[Column(name: 'isActive', type: Types::BOOLEAN, nullable: false)]
    public bool $isActive;

    #[Column(type: Types::BOOLEAN, nullable: false)]
    public bool $isExplicit;

    #[Column(type: Types::INTEGER, nullable: false)]
    public int $priority;

    #[Column(type: Types::INTEGER, nullable:  false)]
    public int $aggressiveness;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    #[ManyToMany(RequestEntity::class, inversedBy: 'responses')]
    #[JoinTable(name: Tables::REQUESTS_RESPONSES_TABLE)]
    public Collection $requests;

    public function __construct()
    {
        $this->createdAt = new DateTime();

        $this->requests = new ArrayCollection();
    }

    #[PreUpdate()]
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function addRequest(RequestEntity $requestEntity) : void
    {
        $this->requests->add($requestEntity);
        $requestEntity->responses->add($this);
    }

    public function removeRequest(RequestEntity $requestEntity) : void
    {
        $this->requests->removeElement($requestEntity);
        $requestEntity->responses->removeElement($this);
    }
}
