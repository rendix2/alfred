<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\AlfredRepository;
use Alfred\App\Model\Tables;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;

/**
 * class AlfredEntity
 *
 * @package Alfred\App\Model\Repository
 */
#[Table(name: Tables::ALFRED_TABLE)]
#[Entity(repositoryClass: AlfredRepository::class)]
#[HasLifecycleCallbacks]
class AlfredEntity
{
    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[Column(name: 'isActive', type: Types::BOOLEAN, nullable: false)]
    public bool $isActive;

    #[Column(type: Types::INTEGER, nullable: false)]
    public int $mood;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $startedAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $finishedAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    #[PreUpdate()]
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }
}
