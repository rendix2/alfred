<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\EventRepository;
use Alfred\App\Model\Tables;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * class EventEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::EVENT_TABLE)]
#[Entity(repositoryClass: EventRepository::class)]
class EventEntity
{

    #[Id()]
    #[GeneratedValue()]
    #[Column(type: Types::INTEGER)]
    public int $id;

    #[Column(name: 'name', length: 512, nullable: false)]
    public string $name;

    #[Column(name: 'description', nullable: true)]
    public ?string $description;

    #[Column(name: 'activeFrom', nullable: true)]
    public ?DateTime $activeFrom;

    #[Column(name: 'activeTo', nullable: true)]
    public ?DateTime $activeTo;

    #[Column(name: 'isActive', type: Types::BOOLEAN, nullable: false)]
    public bool $isActive;

}
