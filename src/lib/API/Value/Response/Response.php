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

class Response
{
    public const TYPE_OK ="ok";
    public const TYPE_ERROR ="error";

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public string $status;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    public int $version;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public string $requestid;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    public int $requests;

    /**
     * @var Error|null
     * @Serializer\Type("AlmaviaCX\Calameo\API\Value\Response\Error")
     */
    public ?Error $error;

    /**
     * @var ResponseContent
     * @Serializer\Type("AlmaviaCX\Calameo\API\Value\Response\ResponseContent")
     */
    public ResponseContent $content;
}
