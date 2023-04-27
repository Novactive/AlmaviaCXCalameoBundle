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
use Closure;
use Ibexa\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    public ?string $publicationId;
    public ?int $folderId;
    /**
     * Input file URI, as a path to a file on a disk.
     *
     * @var string|null
     */
    public ?string $inputUri;
    public Closure $publicationLoader;
    protected ?Publication $publication;

    /**
     * Returns a string representation of the field value.
     */
    public function __toString(): string
    {
        return $this->publicationId;
    }

    /**
     * @inheritDoc
     */
    public function __get($property)
    {
        if ($property === "publication") {
            if (!$this->publication && $this->publicationLoader) {
                $loader = $this->publicationLoader;
                $this->publication = $loader();
            }
        }
        return parent::__get($property);
    }
}
