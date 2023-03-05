<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CamelCaseStrategy.php
 * User: Tomáš Babický
 * Date: 29.10.2021
 * Time: 15:31
 */

namespace Alfred\App\Model;

use Doctrine\ORM\Mapping\NamingStrategy;

/**
 * class CamelCaseStrategy
 *
 * @package Alfred\App\Model
 */
class CamelCaseStrategy implements NamingStrategy
{

    /**
     * @param $className
     *
     * @return string
     */
    public function classToTableName($className)
    {
        $explodedName = explode('\\', $className);
        $count = count($explodedName);

        $entityName = strtolower(str_replace('Entity', '', $explodedName[$count - 1]));

        return $entityName;
    }

    /**
     * @param $propertyName
     * @param $className
     *
     * @return string
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $propertyName;
    }

    /**
     * @param $propertyName
     * @param $embeddedColumnName
     * @param $className
     * @param $embeddedClassName
     *
     * @return string|void
     */
    public function embeddedFieldToColumnName(
        $propertyName,
        $embeddedColumnName,
        $className = null,
        $embeddedClassName = null
    ) {
        // TODO: Implement embeddedFieldToColumnName() method.
    }

    /**
     * @return string
     */
    public function referenceColumnName()
    {
        return 'id';
    }

    /**
     * @param $propertyName
     *
     * @return string
     */
    public function joinColumnName($propertyName)
    {
        $propertyName = str_replace('Entity', '', $propertyName);

        return $propertyName . 'Id';
    }

    /**
     * @param $sourceEntity
     * @param $targetEntity
     * @param $propertyName
     *
     * @return string
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        return $this->classToTableName($sourceEntity) . '2' . $this->classToTableName($targetEntity);
    }

    /**
     * @param $entityName
     * @param $referencedColumnName
     *
     * @return string
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return $this->classToTableName($entityName) . 'Id';
    }
}