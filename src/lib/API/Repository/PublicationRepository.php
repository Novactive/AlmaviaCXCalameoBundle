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
use Psr\Log\LoggerInterface;

class PublicationRepository
{
    public function __construct(
        protected PublicationGateway $gateway,
        protected bool $deleteBookEnable = false,
        private readonly ?LoggerInterface $logger = null,
    )
    {
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
     * @return bool Return false if deleteBookEnable is false
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function deletePublication(string $publicationId): bool
    {
        if (!$this->deleteBookEnable) {
            $this->logger?->info(sprintf(
                '[Calameo] Suppression distante ignorée pour la publication "%s" : almaviacx.calameo.delete_book_enable=false.',
                $publicationId
            ));

            return false;
        }

        $this->gateway->deleteBook($publicationId);

        return true;
    }
}
