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

use AlmaviaCX\Calameo\API\HttpClient;
use AlmaviaCX\Calameo\API\Serializer;
use AlmaviaCX\Calameo\API\Value\Response\Response;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Exception\ExceptionThrower;
use GuzzleHttp\Exception\GuzzleException;

abstract class AbstractGateway
{

    /** @var HttpClient */
    protected $client;

    /** @var Serializer */
    protected $serializer;

    /** @var ExceptionThrower */
    protected $exceptionThrower;

    /**
     * AbstractGateway constructor.
     *
     * @param HttpClient       $client
     * @param Serializer       $serializer
     * @param ExceptionThrower $exceptionThrower
     */
    public function __construct(
        HttpClient $client,
        Serializer $serializer,
        ExceptionThrower $exceptionThrower
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->exceptionThrower = $exceptionThrower;
    }

    /**
     * @param string      $action
     * @param string|null $responseContentType
     * @param array       $requestParameters
     * @param string      $method
     *
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    protected function request(
        string $action,
        ?string $responseContentType,
        array $requestParameters = [],
        string $method = 'GET',
        array $options = []
    ): Response {
        $requestParameters['action'] = $action;

        $clientResponse = $this->client->call(
            $method,
            $this->getEndpoint(),
            $requestParameters,
            $options
        );

        $response = $this->serializer->deserializeResponse(
            $clientResponse,
            $responseContentType
        );
        if ($response->status === Response::TYPE_ERROR) {
            $this->exceptionThrower->throwApiException(
                $response->error->code,
                $response->error->message
            );
        }

        return $response;
    }

    abstract protected function getEndpoint(): string;
}
