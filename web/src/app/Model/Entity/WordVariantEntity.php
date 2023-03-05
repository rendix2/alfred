<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\WordVariantRepository;
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
 * class WordVariantEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(name: Tables::WORD_VARIANT)]
#[Entity(repositoryClass: WordVariantRepository::class)]
class WordVariantEntity
{
    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[ManyToOne(targetEntity: WordEntity::class, inversedBy: 'variants')]
    public WordEntity $word;

    #[Column(type: Types::STRING, nullable: false)]
    public string $variantText;

    #[Column(type: Types::BOOLEAN)]
    public bool $isTypo;

    #[Column(type: Types::BOOLEAN)]
    public bool $isReplacedY;

    #[Column(type: Types::BOOLEAN)]
    public bool $isMissingJ;

    #[Column(type: Types::BOOLEAN)]
    public bool $isSynonymous;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    public DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTime $updatedAt;
}