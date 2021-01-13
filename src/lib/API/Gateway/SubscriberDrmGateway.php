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

use DateTime;
use AlmaviaCX\Calameo\Exception\NotImplementedException;

class SubscriberDrmGateway extends GenericGateway
{
    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function fetchSubscriberDRMSingles(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function fetchSubscriberDRMPeriods(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function fetchSubscriberDRMSeries(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @param string $bookId
     * @throws NotImplementedException
     */
    public function addSubscriberDRMSingle(int $subscriptionId, string $login, string $bookId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @param DateTime $from
     * @param DateTime $to
     * @throws NotImplementedException
     */
    public function addSubscriberDRMPeriod(int $subscriptionId, string $login, DateTime $from, DateTime $to): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @param DateTime $from
     * @param $books
     * @throws NotImplementedException
     */
    public function addSubscriberDRMSerie(int $subscriptionId, string $login, DateTime $from, $books): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @param int $periodId
     * @param DateTime $from
     * @param DateTime $to
     * @throws NotImplementedException
     */
    public function updateSubscriberDRMPeriod(
        int $subscriptionId,
        string $login,
        int $periodId,
        DateTime $from,
        DateTime $to
    ): void {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @param int $serieId
     * @param DateTime $from
     * @param int $books
     * @throws NotImplementedException
     */
    public function updateSubscriberDRMSerie(
        int $subscriptionId,
        string $login,
        int $serieId,
        DateTime $from,
        int $books
    ): void {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @param string $bookId
     * @throws NotImplementedException
     */
    public function deleteSubscriberDRMSingle(int $subscriptionId, string $login, string $bookId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @param int $periodId
     * @throws NotImplementedException
     */
    public function deleteSubscriberDRMPeriod(int $subscriptionId, string $login, int $periodId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @param int $serieId
     * @throws NotImplementedException
     */
    public function deleteSubscriberDRMSerie(int $subscriptionId, string $login, int $serieId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param int $subscriptionId
     * @param string $login
     * @throws NotImplementedException
     */
    public function clearSubscriberDRMs(int $subscriptionId, string $login): void
    {
        throw new NotImplementedException(__METHOD__);
    }
}
