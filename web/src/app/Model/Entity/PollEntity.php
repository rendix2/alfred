<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\PollRepository;
use Alfred\App\Model\Tables;
use DateTime;
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
 * class PollEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::POLL_TABLE)]
#[Entity(repositoryClass: PollRepository::class)]
class PollEntity
{
    #[Id()]
    #[GeneratedValue()]
    #[Column(type: Types::INTEGER)]
    public int $id;

    #[Column(type: Types::STRING)]
    public string $question;

    #[Column(type: Types::STRING)]
    public string $type;

    #[Column(type: Types::BOOLEAN)]
    public bool $allowsMultipleAnswers;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    #[OneToMany(mappedBy: 'poll', targetEntity: PollOptionEntity::class)]
    public Collection $options;

    public function __construct()
    {
        $this->createdAt = new DateTime();

        $this->options = new ArrayCollection();
    }
}
