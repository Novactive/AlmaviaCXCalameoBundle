<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\API\Value\Response;

use JMS\Serializer\Annotation as Serializer;

class Error
{
    /**
     * @var int
     * @Serializer\Type("int")
     */
    public $code;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $message;
}
