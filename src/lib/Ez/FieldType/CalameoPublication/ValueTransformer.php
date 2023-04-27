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

use Ibexa\Contracts\Core\Repository\FieldType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ValueTransformer implements DataTransformerInterface
{
    protected FieldType $fieldType;
    protected Value $initialValue;

    public function __construct(FieldType $fieldType, Value $initialValue)
    {
        $this->fieldType = $fieldType;
        $this->initialValue = $initialValue;
    }

    /**
     * @param Value|null $value
     * @return array
     */
    public function transform(?Value $value): array
    {
        if (null === $value) {
            $value = $this->fieldType->getEmptyValue();
        }

        return [
            'publicationId' => $value->publicationId ?? null,
            'folderId' => $value->folderId ?? null,
            'file' => null,
            'remove' => false,
        ];
    }

    /**
     * @param array|null $value
     * @return Value
     */
    public function reverseTransform(?array $value): Value
    {
        if (!is_array($value)) {
            throw new TransformationFailedException(sprintf('Expected a array got %s', gettype($value)));
        }

        if ($value['remove']) {
            return $this->fieldType->getEmptyValue();
        }

        /* in case file is not modified, overwrite settings only */
        if (null === $value['file']) {
            return clone $this->initialValue;
        }

        return new Value([
            'publicationId' => $value['publicationId'],
            'folderId' => $value['folderId'],
            'inputUri' => $value['file']->getRealPath()
        ]);
    }
}
