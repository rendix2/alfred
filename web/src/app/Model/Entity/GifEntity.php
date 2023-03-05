<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\GifRepository;
use Alfred\App\Model\Tables;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * class GifEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::GIF_TABLE)]
#[Entity(repositoryClass: GifRepository::class)]
class GifEntity
{
    #[Id()]
    #[GeneratedValue()]
    #[Column(type: Types::INTEGER)]
    public int $id;

    #[Column(type: Types::TEXT, nullable: false)]
    public string $url;

    #[Column(type: Types::TEXT, nullable: false)]
    public string $name;

    #[Column(type: Types::BOOLEAN, nullable: false)]
    public bool $isActive;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }
}
