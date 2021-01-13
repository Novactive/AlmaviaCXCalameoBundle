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

use AlmaviaCX\Calameo\API\Value\Publication;
use AlmaviaCX\Calameo\API\Value\Response\Response;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Exception\NotImplementedException;
use GuzzleHttp\Exception\GuzzleException;

class PublicationGateway extends GenericGateway
{
    /**
     * @param string $bookId
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function getBookInfos(string $bookId): Response
    {
        return $this->request(
            'API.getBookInfos',
            Publication::class,
            [
                'book_id' => $bookId
            ]
        );
    }

    /**
     * @param string $bookId
     * @throws NotImplementedException
     */
    public function activateBook(string $bookId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $bookId
     * @throws NotImplementedException
     */
    public function deactivateBook(string $bookId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $bookId
     * @throws NotImplementedException
     */
    public function updateBook(string $bookId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $bookId
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function deleteBook(string $bookId): Response
    {
        return $this->request(
            'API.deleteBook',
            null,
            [
                'book_id' => $bookId
            ]
        );
    }

    /**
     * @param string $bookId
     * @throws NotImplementedException
     */
    public function fetchBookTocs(string $bookId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $bookId
     * @throws NotImplementedException
     */
    public function fetchBookComments(string $bookId): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $bookId
     * @throws NotImplementedException
     */
    public function renewBookPrivateUrl(string $bookId): void
    {
        throw new NotImplementedException(__METHOD__);
    }
}
