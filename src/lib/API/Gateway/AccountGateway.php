<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\API\Gateway;

use AlmaviaCX\Calameo\API\Value\FolderList;
use AlmaviaCX\Calameo\API\Value\Response\Response;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Exception\NotImplementedException;
use GuzzleHttp\Exception\GuzzleException;

class AccountGateway extends GenericGateway
{
    /**
     * @throws NotImplementedException
     */
    public function getAccountInfos(): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $order
     * @param string $way
     * @param int $start
     * @param int $step
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function fetchAccountSubscriptions(
        string $order,
        string $way,
        int $start,
        int $step
    ): Response {
        return $this->request(
            'API.fetchAccountSubscriptions',
            FolderList::class,
            [
                'order' => $order,
                'way' => $way,
                'start' => $start,
                'step' => $step,
            ]
        );
    }

    /**
     * @throws NotImplementedException
     */
    public function fetchAccountBooks(): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @throws NotImplementedException
     */
    public function fetchAccountSubscribers(): void
    {
        throw new NotImplementedException(__METHOD__);
    }
}
