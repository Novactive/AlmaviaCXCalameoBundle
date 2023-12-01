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

use AlmaviaCX\Calameo\API\Gateway\AccountGateway;
use AlmaviaCX\Calameo\API\Value\Folder;
use AlmaviaCX\Calameo\API\Value\FolderList;
use AlmaviaCX\Calameo\API\Value\Request\SortOrder;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use GuzzleHttp\Exception\GuzzleException;

class AccountRepository
{
    protected AccountGateway $gateway;

    public function __construct(AccountGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param string $sortOrder
     * @param string $sortWay
     * @return FolderList
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function fetchAccountFolders(
        int $limit = 20,
        int $offset = 0,
        string $sortOrder = Folder::SORT_NAME,
        string $sortWay = SortOrder::UP
    ): FolderList {
        return $this->gateway->fetchAccountSubscriptions(
            $sortOrder,
            $sortWay,
            $offset,
            $limit
        )->content;
    }
}
