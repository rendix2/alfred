<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\LocationRepository;
use Alfred\App\Model\Tables;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * class LocationEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Entity(repositoryClass: LocationRepository::class)]
#[Table(name: Tables::LOCATION_TABLE)]
class LocationEntity
{

    #[Id()]
    #[GeneratedValue()]
    #[Column(type: Types::INTEGER)]
    public int $id;

    #[Column(type: Types::FLOAT)]
    public float $latitude;

    #[Column(type: Types::FLOAT)]
    public float $longitude;

    #[Column(type: Types::STRING)]
    public string $name;

    #[Column(type: Types::TEXT)]
    public string $description;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }
}
