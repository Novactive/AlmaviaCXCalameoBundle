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

use AlmaviaCX\Calameo\Exception\NotImplementedException;

class SubscriberGateway extends GenericGateway
{
    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function getSubscriberInfos(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function activateSubscriber(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function deactivateSubscriber(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function addSubscriber(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function updateSubscriber(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function deleteSubscriber(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function fetchSubscriberBooks(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function authSubscriberSession(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $sessionId
     * @throws NotImplementedException
     */
    public function checkSubscriberSession(string $sessionId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $sessionId
     * @throws NotImplementedException
     */
    public function deleteSubscriberSession(string $sessionId): void
    {
        throw new NotImplementedException(__METHOD__);
    }
}
