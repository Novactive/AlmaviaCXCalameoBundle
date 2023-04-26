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

use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;

class LegacyConverter implements Converter
{

    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
    }

    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        $fieldSettings = $fieldDef->fieldTypeConstraints->fieldSettings;
        $storageDef->dataText1 = implode('|', $fieldSettings['availableFolderIds'] ?? []);
    }

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        $fieldDef->fieldTypeConstraints->fieldSettings = [
            'availableFolderIds' => explode('|', $storageDef->dataText1 ?? "")
        ];
    }

    public function getIndexColumn(): string
    {
        return 'sort_key_string';
    }
}
