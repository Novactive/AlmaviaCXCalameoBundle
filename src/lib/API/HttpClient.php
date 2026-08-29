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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class HttpClient extends Client
{
    /**
     * Calameo API Key
     */
    protected ?string $APIKey;

    /**
     * CalameoAPI Secret
     */
    protected ?string $APISecret;

    /**
     * HttpClient constructor.
     *
     * @param string|null $APIKey
     * @param string|null $APISecret
     * @param array $config
     */
    public function __construct(?string $APIKey, ?string $APISecret, array $config = [])
    {
        $this->APIKey = $APIKey;
        $this->APISecret = $APISecret;

        parent::__construct($config);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $requestParameters
     * @param array  $options
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function call(
        string $method,
        string $uri = '',
        array $requestParameters = [],
        array $options = []
    ): ResponseInterface {
        $requestParameters['output'] = 'JSON';
        $requestParameters['apikey'] = $this->APIKey;
        if (isset($requestParameters['file'])) {
            /** @var \SplFileInfo $file */
            $file = $requestParameters['file'];
            unset($requestParameters['file']);

            $options['multipart'] = [
                [
                    'name'     => 'file',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => 'custom_filename.pdf'
                ],
            ];
            $method = 'POST';
        }

        $this->signRequest($requestParameters);
        $options['query'] = $requestParameters;

        return $this->request(
            $method,
            $uri,
            $options
        );
    }

    /**
     * http://developer.calameo.com/build/content/api/#sign
     * @param array $requestParameters
     */
    protected function signRequest(array &$requestParameters): void
    {
        $signatureParams = [];
        foreach ($requestParameters as $requestParameterName => $requestParameterValue) {
            $signatureParams[] = sprintf(
                '%s%s',
                $requestParameterName,
                $requestParameterValue
            );
        }

        sort($signatureParams);
        $signature = sprintf(
            '%s%s',
            $this->APISecret,
            implode('', $signatureParams)
        );
        $requestParameters['signature'] = md5($signature);
    }
}
