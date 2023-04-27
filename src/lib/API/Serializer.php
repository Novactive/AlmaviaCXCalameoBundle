<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\API;

use AlmaviaCX\Calameo\API\Value\Response\Response;
use Psr\Http\Message\ResponseInterface;

class Serializer
{
    protected \JMS\Serializer\Serializer $baseSerializer;

    public function __construct(\JMS\Serializer\Serializer $baseSerializer)
    {
        $this->baseSerializer = $baseSerializer;
    }

    public function deserializeResponse(
        ResponseInterface $response,
        ?string $responseContentType
    ): Response {
        $jsonResponse = json_decode(
            $response->getBody()->getContents(),
            true
        );
        if ($responseContentType && isset($jsonResponse['response']['content'])) {
            $jsonResponse['response']['content']['type'] = $responseContentType;
        }

        return $this->baseSerializer->deserialize(
            json_encode($jsonResponse['response']),
            Response::class,
            'json'
        );
    }
}
