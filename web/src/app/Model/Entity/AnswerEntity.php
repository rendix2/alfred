<?php

namespace Alfred\App\Model\Entity;

use Alfred\App\Model\Repository\AnswerRepository;
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
 * class AnswerEntity
 *
 * @package Alfred\App\Model\Entity
 */
#[Table(Tables::ANSWER_TABLE)]
#[Entity(repositoryClass: AnswerRepository::class)]
#[HasLifecycleCallbacks]
class AnswerEntity
{
    #[Column(type: Types::INTEGER)]
    #[Id()]
    #[GeneratedValue()]
    public int $id;

    #[Column(name: 'answerText', type: Types::TEXT, nullable: false)]
    public string $answerText;

/*    #[Column(name: 'isActive', type: Types::BOOLEAN, nullable: false)]
    public bool $isActive;*/

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
