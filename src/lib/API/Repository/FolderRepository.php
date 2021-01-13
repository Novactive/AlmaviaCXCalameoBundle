<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\API\Repository;

use AlmaviaCX\Calameo\API\Gateway\FolderGateway;
use AlmaviaCX\Calameo\API\Value\Folder;
use AlmaviaCX\Calameo\API\Value\Publication;
use AlmaviaCX\Calameo\API\Value\PublicationList;
use AlmaviaCX\Calameo\API\Value\Request\SortOrder;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use GuzzleHttp\Exception\GuzzleException;

class FolderRepository
{
    /** @var FolderGateway */
    protected $gateway;

    /**
     * SubscriptionRepository constructor.
     * @param FolderGateway $gateway
     */
    public function __construct(FolderGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param int $folderId
     * @return Folder
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function getFolderInfos(int $folderId): Folder
    {
        return $this->gateway->getSubscriptionInfos($folderId)->content;
    }

    /**
     * @param int $folderId
     * @param int $limit
     * @param int $offset
     * @param string $sortOrder
     * @param string $sortWay
     * @return PublicationList
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function fetchFolderPublications(
        int $folderId,
        int $limit = 20,
        int $offset = 0,
        string $sortOrder = Publication::SORT_NAME,
        string $sortWay = SortOrder::UP
    ): PublicationList {
        return $this->gateway->fetchSubscriptionBooks(
            $folderId,
            $sortOrder,
            $sortWay,
            $offset,
            $limit
        )->content;
    }
}
