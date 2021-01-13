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
use eZ\Publish\SPI\FieldType\StorageGateway;
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
     * @param array $fieldIds
     * @param int $versionNo
     * @return string[]
     */
    public function getReferencedPublicationIds(array $fieldIds, int $versionNo): array
    {
        if (empty($fieldIds)) {
            return [];
        }

        $selectQuery = $this->connection->createQueryBuilder();
        $selectQuery
            ->select(
                $this->connection->quoteIdentifier('data_text')
            )
            ->from($this->connection->quoteIdentifier('ezcontentobject_attribute'))
            ->where(
                $selectQuery->expr()->andX(
                    $selectQuery->expr()->in(
                        $this->connection->quoteIdentifier('id'),
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
}
