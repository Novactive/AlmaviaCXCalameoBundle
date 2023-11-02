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
use AlmaviaCX\Calameo\Exception\Response\UnknownBookIDException;
use AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Gateway\DoctrineStorage;
use Ibexa\Contracts\Core\FieldType\FieldStorage as FieldStorageInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use SplFileInfo;

class FieldStorage implements FieldStorageInterface
{
    public PublicationRepository $publicationRepository;
    public PublishingService $publishingService;
    public DoctrineStorage $gateway;
    public LoggerInterface $logger;

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
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context): ?bool
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

         $publicationReferenceData = $this->gateway->getPublicationReferenceData($field->id, $versionInfo->versionNo);
        if ($publicationReferenceData === null || !$publicationReferenceData['publicationId']) {
            return;
        }

        $field->value->externalData = $publicationReferenceData;
        // #111471 - [MIG-GOUV] Creation de contenu : dysfonctionnement dans la création de certains contenu
        // https://almaviacx.easyredmine.com/issues/111471?journals=all#note-971094
        // Lors de la publication d'un contenu on a cette erreur :
        // > Lors de la publication d'un contenu on a cette erreur :
        // Au lieu de set le publicationLoader on set directement la publication
//        $field->value->externalData['publicationLoader'] = static function () use ($repository, $field) {
//            try {
//                return $repository->getPublicationInfos($field->value->externalData['publicationId']);
//            } catch (UnknownBookIDException $exception) {
//                return;
//            }
//        };
        if (empty($field->value->externalData['publication'])) {
            try {
                $publication = $repository->getPublicationInfos($field->value->externalData['publicationId']);
                $field->value->externalData['publication'] = $publication;
            } catch (UnknownBookIDException $exception) {
            }
        }
    }

    /**
     * @param VersionInfo $versionInfo
     * @param array $fieldIds
     * @param array $context
     * @return void
     * @throws GuzzleException
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
     * @return \Ibexa\Contracts\Core\Search\Field[]|void
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
    }

    public function copyLegacyField(VersionInfo $versionInfo, Field $field, Field $originalField, array $context): bool
    {
        if ($originalField->value->externalData === null) {
            return false;
        }

        return $this->gateway->storePublicationReference($versionInfo, $field);
    }
}
