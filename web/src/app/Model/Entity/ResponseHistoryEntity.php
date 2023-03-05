<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\ResponseHistoryRepository;
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
 * class ResponseHistory
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::RESPONSE_HISTORY_TABLE)]
#[Entity(repositoryClass: ResponseHistoryRepository::class)]
class ResponseHistoryEntity
{

    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[ManyToOne(targetEntity: RequestEntity::class)]
    public RequestEntity $request;

    #[ManyToOne(targetEntity: ResponseEntity::class)]
    public ResponseEntity $response;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }
}
