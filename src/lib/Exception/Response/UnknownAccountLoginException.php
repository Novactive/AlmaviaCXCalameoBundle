<?php

/**
 * @copyright Novactive
 * Date: 05/02/2021
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Exception\Response;

use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;

class UnknownAccountLoginException extends \Exception implements ApiResponseErrorException
{

}
