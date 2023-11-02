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
    public ?string $publicationId = null;
    public ?int $folderId = null;
    /**
     * Input file URI, as a path to a file on a disk.
     *
     * @var string|null
     */
    public ?string $inputUri = null; // must not be accessed before initialization
    protected ?Publication $publication = null;

    /**
     * Returns a string representation of the field value.
     */
    public function __toString(): string
    {
        return $this->publicationId;
    }
}
