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

use AlmaviaCX\Calameo\API\Value\Folder;
use AlmaviaCX\Calameo\API\Value\PublicationList;
use AlmaviaCX\Calameo\API\Value\Response\Response;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Exception\NotImplementedException;
use GuzzleHttp\Exception\GuzzleException;

class FolderGateway extends GenericGateway
{
    /**
     * @param int $subscriptionId
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function getSubscriptionInfos(int $subscriptionId): Response
    {
        return $this->request(
            'API.getSubscriptionInfos',
            Folder::class,
            [
                'subscription_id' => $subscriptionId
            ]
        );
    }

    /**
     * @param int $subscriptionId
     * @param string $order
     * @param string $way
     * @param int $start
     * @param int $step
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function fetchSubscriptionBooks(
        int $subscriptionId,
        string $order,
        string $way,
        int $start,
        int $step
    ): Response {
        return $this->request(
            'API.fetchSubscriptionBooks',
            PublicationList::class,
            [
                'subscription_id' => $subscriptionId,
                'order' => $order,
                'way' => $way,
                'start' => $start,
                'step' => $step,
            ]
        );
    }

    /**
     * @param int $subscriptionId
     * @throws NotImplementedException
     */
    public function fetchSubscriptionSubscribers(int $subscriptionId): void
    {
        throw new NotImplementedException(__METHOD__);
    }
}
