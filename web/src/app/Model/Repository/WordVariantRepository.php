<?php

namespace Alfred\App\Model\Repository;

use Alfred\App\Model\Entity\WordEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * class WordVariantRepository
 *
 * @package Alfred\App\Model\Repository
 */
class WordVariantRepository extends EntityRepository
{
}
