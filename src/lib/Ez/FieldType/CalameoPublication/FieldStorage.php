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
use Doctrine\DBAL\Exception;
use GuzzleHttp\Exception\GuzzleException;
use Ibexa\Contracts\Core\FieldType\FieldStorage as FieldStorageInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
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
     * @param array $context ["identifier" => "LegacyStorage"]
     * @return bool
     * @throws ApiResponseErrorException
     * @throws GuzzleException
     * @throws Exception
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context): ?bool
    {
        $inputUri = $field->value->externalData['inputUri'] ?? null;
        if ($inputUri) {
            $file = new SplFileInfo($inputUri);
            if ($field->value->externalData['publicationId'] === null) {
                $folderId = $field->value->externalData['folderId'];
                if (!$folderId) { // null ou 0
                    // Est-ce possible ?
                    $this->logger->error(sprintf('[Calameo] FolderId is %s',
                        $folderId === null ? 'null' : $folderId
                    ));
                }

                $name = $versionInfo->contentInfo->name;
                // $name === '' car le contenu n'est pas encore enregistré.
                // Du coup calaméo va mettre : "Custom Filename"
                if (!$name) {
                    $name = 'c' . $versionInfo->contentInfo->id; //
                }

                // Création
                $publication = $this->publishingService->publish(
                    $folderId,
                    $file,
                    [
                        'name' => $name, // expected to be of type "string"
                        'is_published' => 1,
                        'publishing_mode' => Publication::PUBLISHING_MODE_PUBLIC,
                    ]
                );
                $field->value->externalData['publicationId'] = $publication->id;
            } else {
                // Modification
                $this->publishingService->revise(
                    $field->value->externalData['publicationId'],
                    $file
                );
            }
        }

        $this->gateway->storePublicationReference($versionInfo, $field);
        return true;
    }

    /**
     * @param VersionInfo $versionInfo
     * @param Field $field
     * @param array $context
     * @throws Exception
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context): void
    {
        $publicationReferenceData = $this->gateway->getPublicationReferenceData($field->id, $versionInfo->versionNo);
        if ($publicationReferenceData
        && !empty($publicationReferenceData['publicationId'])
        && !empty($publicationReferenceData['folderId'])
        ) {
            $field->value->externalData = $publicationReferenceData;
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

        $publicationIds = $this->gateway->getReferencedPublications($fieldIds);
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
