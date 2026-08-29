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
use AlmaviaCX\Calameo\Exception\CalameoResponseContentMustBePublication;
use AlmaviaCX\Calameo\Exception\Response\UnknownBookIDException;
use AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Gateway\DoctrineStorage;
use Doctrine\DBAL\Exception;
use GuzzleHttp\Exception\GuzzleException;
use Ibexa\Contracts\Core\FieldType\FieldStorage as FieldStorageInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Psr\Log\LoggerInterface;
use SplFileInfo;

readonly class FieldStorage implements FieldStorageInterface
{
    public function __construct(
        protected PublicationRepository $publicationRepository,
        protected PublishingService $publishingService,
        protected DoctrineStorage $gateway,
        protected LoggerInterface $logger,
        protected ConfigResolverInterface $configResolver,
    ) {
    }

    /**
     * @param VersionInfo $versionInfo
     * @param Field $field
     * @return bool
     * @throws ApiResponseErrorException|GuzzleException|Exception|CalameoResponseContentMustBePublication
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field): bool
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
     * @throws Exception
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field,): void
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
     * @return void
     * @throws GuzzleException|Exception
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds): void
    {
        if (empty($fieldIds)) {
            return;
        }

        $publicationIds = $this->gateway->getReferencedPublications($fieldIds);
        $versionPublicationId = $publicationIds[$versionInfo->versionNo] ?? null;

        // On supprime toujours la référence locale.
        // La suppression dans Ibexa ne doit pas être bloquée par une config API Calaméo absente.
        $this->gateway->removePublicationReferences($fieldIds, $versionInfo->versionNo);

        $versionWithPublication = array_keys($publicationIds, $versionPublicationId);

        if (count($versionWithPublication) > 1 || !$versionPublicationId) {
            return;
        }

        if (!$this->isCalameoApiConfigured()) {
            $this->logger->warning(sprintf(
                '[Calameo] Suppression distante ignorée pour la publication "%s" : API key/secret non configurés.',
                $versionPublicationId
            ));

            return;
        }

        if (!$this->isCalameoDeleteBookEnable()) {
            $this->logger->warning(sprintf(
                '[Calameo] Suppression distante ignorée pour la publication "%s" : delete_book_enable=false.',
                $versionPublicationId
            ));

            return;
        }

        try {
            $this->publicationRepository->deletePublication($versionPublicationId);
        } catch (UnknownBookIDException) {
            return;
        } catch (ApiResponseErrorException|GuzzleException $exception) {
            $this->logger->error(sprintf(
                '[Calameo] Impossible de supprimer la publication distante "%s" : %s',
                $versionPublicationId,
                $exception->getMessage()
            ));

            return;
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

    public function copyLegacyField(
        VersionInfo $versionInfo,
        Field $field,
        Field $originalField,
        array $context = []
    ): bool {
        $externalData = $originalField->value->externalData ?? null;

        if (!is_array($externalData)) {
            return false;
        }

        if (empty($externalData['publicationId']) || empty($externalData['folderId'])) {
            return false;
        }

        $field->value->externalData = [
            'publicationId' => $externalData['publicationId'],
            'folderId' => $externalData['folderId'],
        ];

        $this->gateway->storePublicationReference($versionInfo, $field);

        return true;
    }

    private function isCalameoApiConfigured(): bool
    {
        return $this->getCalameoConfigString('api.key') !== ''
            && $this->getCalameoConfigString('api.secret') !== '';
    }

    private function isCalameoDeleteBookEnable(): bool
    {
        return $this->getCalameoConfigString('delete_book_enable') === '1';
    }

    private function getCalameoConfigString(string $name): string
    {
        try {
            $value = $this->configResolver->getParameter(
                sprintf('calameo.%s', $name),
                'almaviacx'
            );
        } catch (\Throwable) {
            return '';
        }

        if (!is_scalar($value)) {
            return '';
        }

        return trim((string) $value);
    }
}
