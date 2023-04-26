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

use AlmaviaCX\Calameo\API\Value\Publication;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\FieldType\Nameable;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\FieldType\Value as BaseValue;
use RuntimeException;

class Type extends FieldType
{
    protected $settingsSchema = [
        'availableFolderIds' => [
            'type' => 'array',
            'default' => [],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function validateFieldSettings($fieldSettings): array
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            if (isset($this->settingsSchema[$name])) {
                switch ($name) {
                    case 'availableFolderIds':
                        if (!\is_array($value)) {
                            $validationErrors[] = new ValidationError(
                                "Setting '%setting%' value must be of array type",
                                null,
                                [
                                    'setting' => $name,
                                ],
                                "[$name]"
                            );
                        }
                        break;
                }
            } else {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        'setting' => $name,
                    ],
                    "[$name]"
                );
            }
        }

        return $validationErrors;
    }

    protected function createValueFromInput($inputValue)
    {
        if (is_array($inputValue)) {
            return new Value([
                'publicationId' => $inputValue['id'] ?? null,
                'inputUri' => $inputValue['inputUri']
            ]);
        }

        return $inputValue;
    }

    /**
     * @param Value $value
     * @throws InvalidArgumentValue
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (isset($value->inputUri)) {
            if (!file_exists($value->inputUri)) {
                throw new InvalidArgumentValue(
                    '$value->inputUri',
                    $value->inputUri,
                    get_class($this)
                );
            }
        }
    }

    public function getFieldTypeIdentifier(): string
    {
        return "calameo_publication";
    }

    /**
     * @param BaseValue $value
     * @param FieldDefinition $fieldDefinition
     * @param string $languageCode
     * @return string
     */
    public function getName(BaseValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        // return (string)$value->text;
        throw new RuntimeException(
            'Name generation provided via NameableField set via "ezpublish.fieldType.nameable" service tag'
        );
    }

    public function getEmptyValue(): Value
    {
        return new Value();
    }

    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return $this->createValueFromInput($hash);
    }

    /**
     * @param Value $value
     * @return array
     */
    public function toHash(SPIValue $value): ?array
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return [
            'id' => $value->publicationId,
            'inputUri' => $value->inputUri
        ];
    }

    /**
     * @param Value $value
     * @return false
     */
    protected function getSortInfo(BaseValue $value): bool
    {
        return false;
    }

    /**
     * @param Value $value
     * @return PersistenceValue
     */
    public function toPersistenceValue(SPIValue $value): PersistenceValue
    {
        return new PersistenceValue(
            [
                'data' => [],
                'externalData' => [
                    'publicationId' => $value->publicationId ?? null,
                    'folderId' => $value->folderId ?? null,
                    'inputUri' => $value->inputUri
                ],
                'sortKey' => $this->getSortInfo($value),
            ]
        );
    }

    /**
     * @param PersistenceValue $fieldValue
     * @return Value
     */
    public function fromPersistenceValue(PersistenceValue $fieldValue): Value
    {
        if ($fieldValue->externalData === null) {
            return $this->getEmptyValue();
        }

        return new Value($fieldValue->externalData);
    }
}
