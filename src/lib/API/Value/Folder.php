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

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use AlmaviaCX\Calameo\API\Value\Response\ResponseContent;

class Folder extends ResponseContent
{
    /** @var string Title of the publication */
    public const SORT_NAME = 'Name';

    /** @var string Date of creation */
    public const SORT_CREATION = 'Creation';

    /** @var string Date the folder was last modified */
    public const SORT_MODIFICATION = 'Modification';

    /**
     * Unique identifying key for the folder
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("ID")
     */
    public int $id;

    /**
     * Unique identifying key for the account of the folder
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("AccountID")
     */
    public int $accountId;

    /**
     * Title of the folder
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("Name")
     */
    public string $name;

    /**
     * Description of the folder
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("Description")
     */
    public string $description;

    /**
     * Available publications inside the folder
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Books")
     */
    public int $books;

    /**
     * Available subscribers inside the folder (only returned for your account's folder)
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Subscribers")
     */
    public int $subscribers;

    /**
     * Date of the folder's creation
     * @var DateTime
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Serializer\SerializedName("Creation")
     */
    public DateTime $creation;

    /**
     * Date of the folder's last modification
     * @var DateTime
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Serializer\SerializedName("Modification")
     */
    public DateTime $modification;

    /**
     * Absolute URL for the folder's overview
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("PublicUrl")
     */
    public string $publicUrl;
}
