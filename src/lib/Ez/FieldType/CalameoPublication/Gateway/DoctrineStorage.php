<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\SPI\FieldType\StorageGateway;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use PDO;

class DoctrineStorage extends StorageGateway
{
    /** @var Connection */
    protected $connection;

    /**
     * DoctrineStorage constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Store the file reference in $field for $versionNo.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     *
     * @return bool
     */
    public function storePublicationReference(VersionInfo $versionInfo, Field $field)
    {
        $referencedData = $this->getPublicationReferenceData($field->id, $versionInfo->versionNo);

        if ($referencedData === null) {
            $this->storeNewFieldData($versionInfo, $field);
        } elseif (is_array($referencedData) && !empty(array_diff_assoc($referencedData, $field->value->externalData))) {
            $this->updateFieldData($versionInfo, $field);
        }

        return false;
    }

    /**
     * Set the required insert columns to insert query builder.
     *
     * This method is intended to be overwritten by derived classes in order to
     * add additional columns to be set in the database. Please do not forget
     * to call the parent when overwriting this method.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    protected function setInsertColumns(QueryBuilder $queryBuilder, VersionInfo $versionInfo, Field $field)
    {
        $queryBuilder
            ->setValue('contentobject_attribute_id', ':fieldId')
            ->setValue('publication_id', ':publicationId')
            ->setValue('folder_id', ':folderId')
            ->setValue('version', ':versionNo')
            ->setParameter(':fieldId', $field->id, PDO::PARAM_INT)
            ->setParameter(':publicationId', $field->value->externalData['publicationId'], PDO::PARAM_STR)
            ->setParameter(':folderId', $field->value->externalData['folderId'], PDO::PARAM_INT)
            ->setParameter(':versionNo', $versionInfo->versionNo, PDO::PARAM_INT)
        ;
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    protected function setUpdateColumns(QueryBuilder $queryBuilder, VersionInfo $versionInfo, Field $field)
    {
        $queryBuilder
            ->set('contentobject_attribute_id', ':fieldId')
            ->set('publication_id', ':publicationId')
            ->set('folder_id', ':folderId')
            ->set('version', ':versionNo')
            ->setParameter(':fieldId', $field->id, PDO::PARAM_INT)
            ->setParameter(':publicationId', $field->value->externalData['publicationId'], PDO::PARAM_STR)
            ->setParameter(':folderId', $field->value->externalData['folderId'], PDO::PARAM_INT)
            ->setParameter(':versionNo', $versionInfo->versionNo, PDO::PARAM_INT)
        ;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    protected function updateFieldData(VersionInfo $versionInfo, Field $field)
    {
        $updateQuery = $this->connection->createQueryBuilder();
        $updateQuery->update(
            $this->connection->quoteIdentifier('calameo_publication')
        );

        $this->setUpdateColumns($updateQuery, $versionInfo, $field);
        $updateQuery
            ->where(
                $updateQuery->expr()->andX(
                    $updateQuery->expr()->eq(
                        $this->connection->quoteIdentifier('contentobject_attribute_id'),
                        ':fieldId'
                    ),
                    $updateQuery->expr()->eq(
                        $this->connection->quoteIdentifier('version'),
                        ':versionNo'
                    )
                )
            )
            ->setParameter(':fieldId', $field->id, PDO::PARAM_INT)
            ->setParameter(':versionNo', $versionInfo->versionNo, PDO::PARAM_INT)
        ;

        $updateQuery->execute();
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    protected function storeNewFieldData(VersionInfo $versionInfo, Field $field)
    {
        $insertQuery = $this->connection->createQueryBuilder();
        $insertQuery->insert(
            $this->connection->quoteIdentifier('calameo_publication')
        );

        $this->setInsertColumns($insertQuery, $versionInfo, $field);

        $insertQuery->execute();
    }

    /**
     * Return $value casted as specified by {@link getPropertyMapping()}.
     *
     * @param mixed $value
     * @param string $columnName
     *
     * @return mixed
     */
    protected function castToPropertyValue($value, $columnName)
    {
        $propertyMap = $this->getPropertyMapping();
        $castFunction = $propertyMap[$columnName]['cast'];

        return $castFunction($value);
    }

    /**
     * Set columns to be fetched from the database.
     *
     * This method is intended to be overwritten by derived classes in order to
     * add additional columns to be fetched from the database. Please do not
     * forget to call the parent when overwriting this method.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param int $fieldId
     * @param int $versionNo
     */
    protected function setFetchColumns(QueryBuilder $queryBuilder, $fieldId, $versionNo)
    {
        $queryBuilder->select(
            $this->connection->quoteIdentifier('publication_id'),
            $this->connection->quoteIdentifier('folder_id')
        );
    }

    /**
     * Return the file reference data for the given $fieldId in $versionNo.
     *
     * @param int $fieldId
     * @param int $versionNo
     *
     * @return array|null
     */
    public function getPublicationReferenceData(int $fieldId, int $versionNo)
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $this->setFetchColumns($selectQuery, $fieldId, $versionNo);

        $selectQuery
            ->from($this->connection->quoteIdentifier('calameo_publication'))
            ->where(
                $selectQuery->expr()->andX(
                    $selectQuery->expr()->eq(
                        $this->connection->quoteIdentifier('contentobject_attribute_id'),
                        ':fieldId'
                    ),
                    $selectQuery->expr()->eq(
                        $this->connection->quoteIdentifier('version'),
                        ':versionNo'
                    )
                )
            )
            ->setParameter(':fieldId', $fieldId, PDO::PARAM_INT)
            ->setParameter(':versionNo', $versionNo, PDO::PARAM_INT)
        ;

        $statement = $selectQuery->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) < 1) {
            return null;
        }

        $convertedResult = [];
        foreach (reset($result) as $column => $value) {
            $convertedResult[$this->toPropertyName($column)] = $this->castToPropertyValue($value, $column);
        }

        return $convertedResult;
    }

    /**
     * Return a set o file references, referenced by the given $fieldIds.
     *
     * @param array $fieldIds
     *
     * @return array
     */
    public function getReferencedPublications(array $fieldIds, $versionNo)
    {
        if (empty($fieldIds)) {
            return [];
        }

        $selectQuery = $this->connection->createQueryBuilder();
        $selectQuery
            ->select(
                $this->connection->quoteIdentifier('publication_id')
            )
            ->from($this->connection->quoteIdentifier('calameo_publication'))
            ->where(
                $selectQuery->expr()->andX(
                    $selectQuery->expr()->in(
                        $this->connection->quoteIdentifier('contentobject_attribute_id'),
                        ':fieldIds'
                    ),
                    $selectQuery->expr()->eq(
                        $this->connection->quoteIdentifier('version'),
                        ':versionNo'
                    )
                )
            )
            ->setParameter(':fieldIds', $fieldIds, Connection::PARAM_INT_ARRAY)
            ->setParameter(':versionNo', $versionNo, PDO::PARAM_INT)
        ;

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::COLUMN);
    }

    /**
     * Return the property name for the given $columnName.
     *
     * @param string $columnName
     *
     * @return string
     */
    protected function toPropertyName($columnName)
    {
        $propertyMap = $this->getPropertyMapping();

        return $propertyMap[$columnName]['name'];
    }

    /**
     * Remove all file references for the given $fieldIds.
     *
     * @param array $fieldIds
     * @param int $versionNo
     */
    public function removePublicationReferences(array $fieldIds, $versionNo)
    {
        if (empty($fieldIds)) {
            return;
        }

        $deleteQuery = $this->connection->createQueryBuilder();
        $deleteQuery
            ->delete($this->connection->quoteIdentifier('calameo_publication'))
            ->where(
                $deleteQuery->expr()->andX(
                    $deleteQuery->expr()->in(
                        $this->connection->quoteIdentifier('contentobject_attribute_id'),
                        ':fieldIds'
                    ),
                    $deleteQuery->expr()->eq(
                        $this->connection->quoteIdentifier('version'),
                        ':versionNo'
                    )
                )
            )
            ->setParameter(':fieldIds', $fieldIds, Connection::PARAM_INT_ARRAY)
            ->setParameter(':versionNo', $versionNo, PDO::PARAM_INT)
        ;

        $deleteQuery->execute();
    }

    /**
     * Return a column to property mapping for the storage table.
     *
     * @return array
     */
    protected function getPropertyMapping()
    {
        return [
            'publication_id' => [
                'name' => 'publicationId',
                'cast' => 'strval',
            ],
            'folder_id' => [
                'name' => 'folderId',
                'cast' => 'intval',
            ]
        ];
    }
}
