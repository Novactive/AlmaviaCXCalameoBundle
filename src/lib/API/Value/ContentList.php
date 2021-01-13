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
use AlmaviaCX\Calameo\API\Value\Response\ResponseContent;

abstract class ContentList extends ResponseContent
{

    /**
     * @var int
     * @Serializer\Type("int")
     */
    public $total;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    public $start;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    public $step;
}
