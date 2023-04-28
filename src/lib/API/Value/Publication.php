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
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Publication extends ResponseContent
{
    /** @var string Title of the publication */
    public const SORT_NAME = "Name";
    /** @var string Number of pages of the publication */
    public const SORT_PAGES = "Pages";
    /** @var string Number of comments on the publication */
    public const SORT_COMMENTS = "Comments";
    /** @var string Number of times the publication was read */
    public const SORT_VIEWS = "Views";
    /** @var string Date of publication */
    public const SORT_DATE = "Date";
    /** @var string Date of creation */
    public const SORT_CREATION = "Creation";
    /** @var string Date the publication was last modified */
    public const SORT_MODIFICATION = "Modification";

    /** @var string waiting to be converted */
    public const STATUS_QUEUE = 'QUEUE';
    /** @var string processing document */
    public const STATUS_PROCESS = 'PROCESS';
    /** @var string converting document */
    public const STATUS_STORE = 'STORE';
    /** @var string error during conversion */
    public const STATUS_ERROR = 'ERROR';
    /** @var string publication ready */
    public const STATUS_DONE = 'DONE';

    public const PUBLISHING_MODE_PUBLIC = 1;
    public const PUBLISHING_MODE_PRIVATE = 2;

    /**
     * ID of the publication
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("ID")
     */
    public string $id;

    /**
     * Publication's owner account ID (should be your account ID)
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("AccountID")
     */
    public int $accountId;

    /**
     * Publication's owner subscription ID
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("SubscriptionID")
     */
    public int $folderId;

    /**
     * Title of the publication
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("Name")
     */
    public string $name;

    /**
     * Description of the publication
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("Description")
     */
    public string $description;

    /**
     * Conversion status of the publication.
     * Either QUEUE (waiting to be converted),
     * PROCESS (processing document),
     * STORE (converting document),
     * ERROR (error during conversion)
     * or DONE (publication ready)
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("Status")
     */
    public string $status;

    /**
     * Sends 1 if the publication is private and 0 if not
     *
     * @var bool
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("IsPrivate")
     */
    public bool $isPrivate;

    /**
     * Authentication parameter for private URLs (authid)
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("AuthID")
     */
    public string $authId;

    /**
     * Sends 1 if the publication allows access to the miniCalaméo and 0 if not
     *
     * @var bool
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("AllowMini")
     */
    public bool $allowMini;

    /**
     * Number of pages of the publication
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Pages")
     */
    public int $pages;

    /**
     * Width of a page of the publication
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Width")
     */
    public int $width;

    /**
     * Height of a page of the publication
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Height")
     */
    public int $height;

    /**
     * Number of views of the publication
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Views")
     */
    public int $views;

    /**
     * Number of downloads of the publication
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Downloads")
     */
    public int $downloads;

    /**
     * Number of comments of the publication
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Comments")
     */
    public int $comments;

    /**
     * Number of favorites from the publication
     *
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("Favorites")
     */
    public int $favorites;

    /**
     * Reference date of the publication
     *
     * @var DateTime
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\SerializedName("Date")
     * @Serializer\Accessor(setter="setDate")
     */
    public DateTime $date;
    /**
     * Date of creation of the publication
     *
     * @var DateTime
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Serializer\SerializedName("Creation")
     */
    public DateTime $creation;
    /**
     * Scheduled date of the publication (UTC)
     *
     * @var DateTime
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Serializer\SerializedName("Publication")
     */
    public DateTime $publication;
    /**
     * Date of the last modification of the publication
     *
     * @var DateTime
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Serializer\SerializedName("Modification")
     */
    public DateTime $modification;
    /**
     * Absolute URL for the publication's larger cover
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("PosterUrl")
     */
    public string $posterUrl;
    /**
     * Absolute URL for the publication's cover
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("PictureUrl")
     */
    public string $pictureUrl;
    /**
     * Absolute URL for the publication's thumbnail
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("ThumbUrl")
     */
    public string $thumbUrl;
    /**
     * Absolute URL for the publication's overview
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("PublicUrl")
     */
    public string $publicUrl;
    /**
     * Absolute URL for the publication's reading page
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("ViewUrl")
     */
    public string $viewUrl;

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $date->setTime(0, 0);
        $this->date = $date;
    }
}
