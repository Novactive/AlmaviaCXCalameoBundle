<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\API\Gateway;

abstract class GenericGateway extends AbstractGateway
{
    public const ENDPOINT = 'http://api.calameo.com/1.0';

    protected function getEndpoint(): string
    {
        return self::ENDPOINT;
    }
}
