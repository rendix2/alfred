<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\PollOptionRepository;
use Alfred\App\Model\Repository\PollRepository;
use Alfred\App\Model\Tables;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * class PollOptionEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::POLL_OPTION_TABLE)]
#[Entity(repositoryClass: PollOptionRepository::class)]
class PollOptionEntity
{
    #[Id()]
    #[GeneratedValue()]
    #[Column(type: Types::INTEGER)]
    public int $id;

    #[ManyToOne(targetEntity: PollEntity::class)]
    public PollEntity $poll;

    #[Column(type: Types::TEXT)]
    public string $optionText;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }
}
