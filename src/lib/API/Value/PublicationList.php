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

class PublicationList extends ContentList
{
    /**
     * @var Publication[]
     * @Serializer\Type("array<AlmaviaCX\Calameo\API\Value\Publication>")
     */
    public $items;
}
