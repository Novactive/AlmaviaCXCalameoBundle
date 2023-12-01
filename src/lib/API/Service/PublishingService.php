<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\API\Service;

use AlmaviaCX\Calameo\API\Gateway\UploadGateway;
use AlmaviaCX\Calameo\API\Value\Publication;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Exception\NotImplementedException;
use GuzzleHttp\Exception\GuzzleException;
use SplFileInfo;

class PublishingService
{
    protected UploadGateway $gateway;

    public function __construct(UploadGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param int $folderId
     * @param SplFileInfo $file
     * @return Publication
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function publish(
        int $folderId,
        SplFileInfo $file,
        array $options = []
    ): Publication {
        $response = $this->gateway->publish(
            $folderId,
            $file,
            $options
        );
        return $response->content;
    }

    /**
     * @param int $folderId
     * @param string $url
     * @param array $options
     * @return Publication
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function publishFromUrl(int $folderId, string $url, array $options = []): Publication
    {
        $response = $this->gateway->publishFromUrl(
            $folderId,
            $url,
            $options
        );
        return $response->content;
    }

    /**
     * @param int $folderId
     * @param string $text
     * @return Publication
     * @throws NotImplementedException
     */
    public function publishFromText(int $folderId, string $text): Publication
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $publicationId
     * @param SplFileInfo $file
     * @return Publication
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function revise(string $publicationId, SplFileInfo $file): Publication
    {
        $response = $this->gateway->revise(
            $publicationId,
            $file
        );
        return $response->content;
    }

    /**
     * @param string $publicationId
     * @param string $url
     * @param int $folderId
     * @return Publication
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function reviseFromUrl(string $publicationId, string $url, int $folderId): Publication
    {
        $response = $this->gateway->reviseFromUrl(
            $publicationId,
            $url,
            $folderId
        );
        return $response->content;
    }

    /**
     * @param string $publicationId
     * @param string $text
     * @param int $folderId
     * @return Publication
     * @throws NotImplementedException
     */
    public function reviseFromText(string $publicationId, string $text, int $folderId): Publication
    {
        throw new NotImplementedException(__METHOD__);
    }
}
