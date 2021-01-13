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
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadGateway extends AbstractGateway
{
    public const ENDPOINT = 'http://upload.calameo.com/1.0';

    /**
     * @param int   $subscriptionId
     * @param       $file
     * @param array $options
     *
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function publish(
        int $subscriptionId,
        $file,
        array $options = []
    ): Response {
        $requestParameters = $this->resolverOptions($options);
        $requestParameters['subscription_id'] = $subscriptionId;
        $requestParameters['file'] = $file;

        return $this->request(
            'API.publish',
            Publication::class,
            $requestParameters
        );
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function resolverOptions(array $options): array
    {
        $optionResolver = new OptionsResolver();
        $optionResolver->setDefaults(
            [
                'name'            => null,
                'description'     => null,
                'date'            => null,
                'is_published'    => null,
                'publishing_mode' => null,
                'publish_date'    => null,
                'private_url'     => null,
                'view'            => null,
                'subscribe'       => null,
                'comment'         => null,
                'download'        => null,
                'print'           => null,
                'share'           => null,
                'adult'           => null,
                'direction'       => null,
                'license'         => null,
                'skin_url'        => null,
                'logo_url'        => null,
                'logo_link_url'   => null,
                'background_url'  => null,
                'music'           => null,
                'music_url'       => null,
                'sfx'             => null,
                'sfx_url'         => null,
            ]
        );
        // Title of the publication. If not present, the filename will be used
        $optionResolver->setAllowedTypes('name', ['null', 'string']);
        // Description of the publication. If not present, the first page'$s text will be used
        $optionResolver->setAllowedTypes('description', ['null', 'string']);
        // Date of the publication for DRM management (YYYY-MM-DD)
        $optionResolver->setAllowedTypes('date', ['null', 'string']);
        // Activation status. Either 0 (disabled) or 1 (enabled)
        $optionResolver->setAllowedTypes('is_published', ['null', 'int']);
        // Access to the publication. Either 1 (public) or 2 (private)
        $optionResolver->setAllowedTypes('publishing_mode', ['null', 'int']);
        // Date and time (UTC) of the publication scheduled publishing (YYYY-MM-DD HH:MM:SS).
        $optionResolver->setAllowedTypes('publish_date', ['null', 'string']);
        // Use a private URL. Either 0 (disabled) or 1 (enabled)
        $optionResolver->setAllowedTypes('private_url', ['null', 'int']);
        // Default viewing mode. Either book, slide, scroll
        $optionResolver->setAllowedTypes('view', ['null', 'string']);
        // Allow subscribers' access. Either 0 (disabled) or 1 (enabled)
        $optionResolver->setAllowedTypes('subscribe', ['null', 'int']);
        // Comments behavior. Either 0 (disabled), 1 (moderate all) or 4 (accept all)
        $optionResolver->setAllowedTypes('comment', ['null', 'int']);
        // Download behavior. Either 0 (disabled) or 2 (everyone)
        $optionResolver->setAllowedTypes('download', ['null', 'int']);
        // Print behavior. Either 0 (disabled) or 2 (everyone)
        $optionResolver->setAllowedTypes('print', ['null', 'int']);
        // Share menu. Either 0 (disabled), 1 (enabled). Enabled by default
        $optionResolver->setAllowedTypes('share', ['null', 'int']);
        // Restrict access to adults. Either 0 (no) or 1 (yes)
        $optionResolver->setAllowedTypes('adult', ['null', 'int']);
        // Reading direction. Either 0 (left-to-right) or 1 (right-to-left "manga mode")
        $optionResolver->setAllowedTypes('direction', ['null', 'int']);
        // License. Either <empty> (traditionnal copyright) or pd (public domain),
        // by, by_nc, by_nc_nd, by_nc_sa, by_nd or by_sa (Creative Commons)
        $optionResolver->setAllowedTypes('license', ['null', 'string']);
        // Custom skin URL. Must be an absolute URL
        $optionResolver->setAllowedTypes('skin_url', ['null', 'string']);
        // Custom logo URL. Must be an absolute URL
        $optionResolver->setAllowedTypes('logo_url', ['null', 'string']);
        // Custom logo link URL. Must be an absolute URL
        $optionResolver->setAllowedTypes('logo_link_url', ['null', 'string']);
        // Custom background URL. Must be an absolute URL
        $optionResolver->setAllowedTypes('background_url', ['null', 'string']);
        // Background music mode. Either 0 (loop forever), 1 (play only once)
        $optionResolver->setAllowedTypes('music', ['null', 'int']);
        // Custom background music URL. Must be an absolute URL
        $optionResolver->setAllowedTypes('music_url', ['null', 'string']);
        // Play sound effects like page flipping. Either 0 (disabled) or 1 (enabled)
        $optionResolver->setAllowedTypes('sfx', ['null', 'int']);
        // Custom page flipping sound URL. Must be an absolute URL
        $optionResolver->setAllowedTypes('sfx_url', ['null', 'string']);

        return array_filter($optionResolver->resolve($options));
    }

    /**
     * @param int    $subscriptionId ID of the folder
     * @param string $url            URL of the document to publish
     * @param array  $options
     *
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function publishFromUrl(
        int $subscriptionId,
        string $url,
        array $options = []
    ): Response {
        $requestParameters = $this->resolverOptions($options);
        $requestParameters['subscription_id'] = $subscriptionId;
        $requestParameters['url'] = $url;

        return $this->request(
            'API.publishFromUrl',
            Publication::class,
            $requestParameters
        );
    }

    /**
     * @param int    $subscriptionId
     * @param string $text
     * @param array  $options
     *
     * @throws NotImplementedException
     */
    public function publishFromText(
        int $subscriptionId,
        string $text,
        array $options = []
    ): void {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $bookId
     * @param        $file
     *
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function revise(
        string $bookId,
        $file
    ): Response {
        $requestParameters = [
            'book_id' => $bookId,
            'file'    => $file,
        ];

        return $this->request(
            'API.revise',
            Publication::class,
            $requestParameters
        );
    }

    /**
     * @param string $bookId
     * @param string $url
     * @param int    $subscriptionId
     *
     * @return Response
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function reviseFromUrl(
        string $bookId,
        string $url,
        int $subscriptionId
    ): Response {
        $requestParameters = [
            'book_id'         => $bookId,
            'subscription_id' => $subscriptionId,
            'url'             => $url,
        ];

        return $this->request(
            'API.reviseFromUrl',
            Publication::class,
            $requestParameters
        );
    }

    /**
     * @param string $bookId
     * @param string $text
     * @param int    $subscriptionId
     *
     * @throws NotImplementedException
     */
    public function reviseFromText(
        string $bookId,
        string $text,
        int $subscriptionId
    ): void {
        throw new NotImplementedException(__METHOD__);
    }

    protected function getEndpoint(): string
    {
        return self::ENDPOINT;
    }
}
