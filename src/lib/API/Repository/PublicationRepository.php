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

use AlmaviaCX\Calameo\API\Gateway\PublicationGateway;
use AlmaviaCX\Calameo\API\Value\Publication;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use GuzzleHttp\Exception\GuzzleException;

class PublicationRepository
{
    protected PublicationGateway $gateway;

    public function __construct(PublicationGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param string $publicationId
     * @return Publication
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function getPublicationInfos(string $publicationId): Publication
    {
        return $this->gateway->getBookInfos($publicationId)->content;
    }

    /**
     * @param string $publicationId
     * @return bool
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function deletePublication(string $publicationId): bool
    {
        $this->gateway->deleteBook($publicationId);
        return true;
    }
}
