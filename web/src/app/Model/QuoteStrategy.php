<?php
/**
 *
 * Created by PhpStorm.
 * Filename: QuoteStrategy.php
 * User: Tomáš Babický
 * Date: 05.04.2022
 * Time: 22:09
 */

namespace Alfred\App\Model;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;

/**
 * class QuoteStrategy
 *
 * @package Alfred\App\Model
 */
class QuoteStrategy extends DefaultQuoteStrategy
{

    /**
     * @param                  $token
     * @param AbstractPlatform $platform
     *
     * @return string|array
     */
    private function quote($token, AbstractPlatform $platform) : string|array
    {
        if (is_array($token)) {
            return $token;
        }

        // implement your quote strategy
        switch ($platform->getName()) {
            case 'mysql':
            default:
                return '`' . $token . '`';
        }
    }

    // add quoting to appropriate methods

    /**
     * @param                  $fieldName
     * @param ClassMetadata    $class
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getColumnName($fieldName, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $this->quote(
            parent::getColumnName($fieldName, $class, $platform),
            $platform
        );
    }

    /**
     * @param array            $association
     * @param ClassMetadata    $class
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getJoinTableName(array $association, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $this->quote(
            parent::getJoinTableName($association, $class, $platform),
            $platform
        );
    }

    /**
     * @param ClassMetadata    $class
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getTableName(ClassMetadata $class, AbstractPlatform $platform)
    {
        return $this->quote(
            parent::getTableName($class, $platform),
            $platform
        );
    }

    /**
     * @param array            $joinColumn
     * @param ClassMetadata    $class
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $this->quote(
            parent::getJoinColumnName($joinColumn, $class, $platform),
            $platform
        );
    }

    /**
     * @param array            $joinColumn
     * @param ClassMetadata    $class
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getReferencedJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $this->quote(
            parent::getReferencedJoinColumnName($joinColumn, $class, $platform),
            $platform
        );
    }

    /**
     * @param ClassMetadata    $class
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getIdentifierColumnNames(ClassMetadata $class, AbstractPlatform $platform)
    {
        return $this->quote(
            parent::getIdentifierColumnNames($class, $platform),
            $platform
        );
    }
}
