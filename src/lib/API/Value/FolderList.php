<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\API\Value;

use JMS\Serializer\Annotation as Serializer;

class FolderList extends ContentList
{
    /**
     * @var Folder[]
     * @Serializer\Type("array<AlmaviaCX\Calameo\API\Value\Folder>")
     */
    public $items;
}
