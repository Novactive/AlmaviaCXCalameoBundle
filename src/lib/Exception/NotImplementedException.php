<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Exception;

use Exception;

class NotImplementedException extends Exception
{
    /**
     * Generates: Intentionally not implemented: {$feature}.
     *
     * @param string $feature
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $feature, $code = 0, Exception $previous = null)
    {
        parent::__construct("Intentionally not implemented: {$feature}", $code, $previous);
    }
}
