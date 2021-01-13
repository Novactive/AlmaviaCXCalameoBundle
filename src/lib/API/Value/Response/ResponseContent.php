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

/**
 * Interface ResponseContent
 * @package AlmaviaCX\Calameo\API\Value\Response
 *
 * @Serializer\Discriminator(
 *     field = "type",
 *     map = {
 *         "AlmaviaCX\Calameo\API\Value\Folder": "AlmaviaCX\Calameo\API\Value\Folder",
 *         "AlmaviaCX\Calameo\API\Value\FolderList": "AlmaviaCX\Calameo\API\Value\FolderList",
 *         "AlmaviaCX\Calameo\API\Value\PublicationList": "AlmaviaCX\Calameo\API\Value\PublicationList",
 *         "AlmaviaCX\Calameo\API\Value\Publication": "AlmaviaCX\Calameo\API\Value\Publication"
 *     }
 * )
 */
abstract class ResponseContent
{
}
