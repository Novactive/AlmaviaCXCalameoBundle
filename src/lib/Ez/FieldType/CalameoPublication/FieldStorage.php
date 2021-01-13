<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication;

use AlmaviaCX\Calameo\API\Repository\PublicationRepository;
use AlmaviaCX\Calameo\API\Service\PublishingService;
use AlmaviaCX\Calameo\API\Value\Publication;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Exception\NotImplementedException;
use AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Gateway\DoctrineStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use eZ\Publish\SPI\FieldType\FieldStorage as FieldStorageInterface;
use GuzzleHttp\Exception\GuzzleException;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FieldStorage implements FieldStorageInterface
{
    /** @var PublicationRepository */
    public $publicationRepository;

    /** @var PublishingService */
    public $publishingService;

    /** @var DoctrineStorage */
    public $gateway;

    /**
     * FieldStorage constructor.
     *
     * @param PublicationRepository $publicationRepository
     * @param PublishingService     $publishingService
     * @param DoctrineStorage       $gateway
     */
    public function __construct(
        PublicationRepository $publicationRepository,
        PublishingService $publishingService,
        DoctrineStorage $gateway
    ) {
        $this->publicationRepository = $publicationRepository;
        $this->publishingService = $publishingService;
        $this->gateway = $gateway;
    }

    /**
     * @param VersionInfo $versionInfo
     * @param Field $field
     * @param array $context
     * @return bool
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context): bool
    {
        $inputUri = $field->value->externalData['inputUri'] ?? null;
        if ($inputUri) {
            $file = new SplFileInfo($inputUri);
            if ($field->value->data['publicationId'] !== null) {
                $publication = $this->publishingService->revise(
                    $field->value->data['publicationId'],
                    $file
                );
            } else {
                $publication = $this->publishingService->publish(
                    $field->value->data['folderId'],
                    $file,
                    [
                        'is_published' => 1,
                        'publishing_mode' => Publication::PUBLISHING_MODE_PUBLIC,
                    ]
                );
                $field->value->data['publicationId'] = $publication->id;
            }
        }

        return true;
    }

    /**
     * @param VersionInfo $versionInfo
     * @param Field $field
     * @param array $context
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context): void
    {
        $repository = $this->publicationRepository;
        $field->value->externalData['publicationLoader'] = static function () use ($repository, $field) {
            try {
                return $repository->getPublicationInfos($field->value->data['publicationId']);
            } catch (ApiResponseErrorException $exception) {
                // 501 = Unknown book ID
                if ($exception->getCode() !== 501) {
                    throw $exception;
                }
            }
            return null;
        };
    }

    /**
     * @param VersionInfo $versionInfo
     * @param array $fieldIds
     * @param array $context
     * @return bool|void
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context): void
    {
        if ($versionInfo->status === VersionInfo::STATUS_PUBLISHED) {
            $publicationIds = $this->gateway->getReferencedPublicationIds($fieldIds, $versionInfo->versionNo);
            foreach ($publicationIds as $publicationId) {
                try {
                    $this->publicationRepository->deletePublication($publicationId);
                } catch (ApiResponseErrorException $exception) {
                    // 501 = Unknown book ID
                    if ($exception->getCode() !== 501) {
                        throw $exception;
                    }
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function hasFieldData(): bool
    {
        return true;
    }

    /**
     * @param VersionInfo $versionInfo
     * @param Field $field
     * @param array $context
     * @return \eZ\Publish\SPI\Search\Field[]|void
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
    }

    public function copyLegacyField(VersionInfo $versionInfo, Field $field, Field $originalField, array $context)
    {
        return false;
    }
}
