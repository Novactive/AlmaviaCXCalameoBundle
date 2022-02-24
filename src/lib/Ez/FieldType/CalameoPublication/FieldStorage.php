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
use AlmaviaCX\Calameo\Exception\Response\ApiResponseException;
use AlmaviaCX\Calameo\Exception\Response\UnknownBookIDException;
use AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Gateway\DoctrineStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use eZ\Publish\SPI\FieldType\FieldStorage as FieldStorageInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
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

    /** @var LoggerInterface */
    public $logger;

    /**
     * @param PublicationRepository $publicationRepository
     * @param PublishingService     $publishingService
     * @param DoctrineStorage       $gateway
     * @param LoggerInterface       $logger
     */
    public function __construct(
        PublicationRepository $publicationRepository,
        PublishingService $publishingService,
        DoctrineStorage $gateway,
        LoggerInterface $logger
    ) {
        $this->publicationRepository = $publicationRepository;
        $this->publishingService = $publishingService;
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    /**
     * @param VersionInfo $versionInfo
     * @param Field $field
     * @param array $context
     * @return bool
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $inputUri = $field->value->externalData['inputUri'] ?? null;
        if ($inputUri) {
            $file = new SplFileInfo($inputUri);
            try {
                if ($field->value->externalData['publicationId'] === null) {
                    throw new UnknownBookIDException();
                }
                $publication = $this->publishingService->revise(
                    $field->value->externalData['publicationId'],
                    $file
                );
            } catch (UnknownBookIDException $exception) {
                $publication = $this->publishingService->publish(
                    $field->value->externalData['folderId'],
                    $file,
                    [
                        'name' => $versionInfo->contentInfo->name,
                        'is_published' => 1,
                        'publishing_mode' => Publication::PUBLISHING_MODE_PUBLIC,
                    ]
                );
                $field->value->externalData['publicationId'] = $publication->id;
            }
        }

        $this->gateway->storePublicationReference($versionInfo, $field);
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

         $publicationReferenceData = $this->gateway->getPublicationReferenceData($field->id, $versionInfo->versionNo);
        if ($publicationReferenceData === null || !$publicationReferenceData['publicationId']) {
            return;
        }

        $field->value->externalData = $publicationReferenceData;
        $field->value->externalData['publicationLoader'] = static function () use ($repository, $field) {
            try {
                return $repository->getPublicationInfos($field->value->externalData['publicationId']);
            } catch (UnknownBookIDException $exception) {
                return;
            }
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
        if (empty($fieldIds)) {
            return;
        }

        $publicationIds = $this->gateway->getReferencedPublications($fieldIds, $versionInfo->versionNo);
        $versionPublicationId = $publicationIds[$versionInfo->versionNo] ?? null;
        $this->gateway->removePublicationReferences($fieldIds, $versionInfo->versionNo);

        $versionWithPublication = array_keys($publicationIds, $versionPublicationId);
        if (count($versionWithPublication) <= 1 && $versionPublicationId) {
            try {
                $this->publicationRepository->deletePublication($versionPublicationId);
            } catch (UnknownBookIDException $exception) {
                return ;
            } catch (ApiResponseErrorException $exception) {
                $this->logger->error($exception->getMessage());
                return ;
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
//        if ($field->id !== $originalField->id) {
//            var_dump([
//                $versionInfo,
//                $field,
//                $originalField,
//                $context
//            ]);
//            die;
//        }
        if ($originalField->value->externalData === null) {
            return false;
        }

        return $this->gateway->storePublicationReference($versionInfo, $field);
    }
}
