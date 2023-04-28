<?php

/**
 * @copyright Novactive
 * Date: 05/02/2021
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Exception;

use AlmaviaCX\Calameo\Exception\Response;

class ExceptionThrower
{
    private array $exceptionsByCode = [
        99   => Response\TooManyRequestsException::class,
        101  => Response\InvalidSignatureException::class,
        102  => Response\InvalidEndpointException::class,
        103  => Response\InvalidAccountTypeException::class,
        104  => Response\InvalidAPIKeyException::class,
        105  => Response\InvalidAccountAPIKeyException::class,
        106  => Response\RequestExpiredException::class,
        107  => Response\AccessDeniedException::class,
        201  => Response\UnknownAccountIdException::class,
        301  => Response\UnknownAccountLoginException::class,
        302  => Response\MissingOrIncorrectParameterException::class,
        303  => Response\DocumentUploadSpamErrorException::class,
        401  => Response\UnknownFolderIDException::class,
        501  => Response\UnknownBookIDException::class,
        502  => Response\BookNotActivatedException::class,
        503  => Response\PublicationIsNotPrivateException::class,
        504  => Response\PublicationIsNotPublishedException::class,
        505  => Response\PublicationIsNotRevisedException::class,
        506  => Response\PublicationIsNotUpdatedException::class,
        507  => Response\PublicationIsNotDeletedException::class,
        509  => Response\PublicationPrivateURLNotRenewedException::class,
        511  => Response\MaximumNumberOfPrivatePublicationsReachedException::class,
        601  => Response\UnknownSubscriberIDException::class,
        602  => Response\TheSubscriberHasNotBeenAddedException::class,
        603  => Response\TheSubscriberHasNotBeenUpdatedException::class,
        604  => Response\TheSubscriberHasNotBeenActivatedException::class,
        605  => Response\TheSubscriberHasNotBeenDeactivatedException::class,
        606  => Response\TheSubscriberHasNotBeenUnsubscribedException::class,
        607  => Response\TheSubscriberHasNotBeenDeletedException::class,
        608  => Response\TheSubscriberSessionHasNotBeenCreatedException::class,
        609  => Response\TheSubscriberSessionHasNotBeenDeletedException::class,
        610  => Response\UnknownSubscriberSessionIDException::class,
        611  => Response\MaximumNumberOfSubscriberReachedException::class,
        801  => Response\TheUnitSubscriptionHasNotBeenAddedException::class,
        802  => Response\TheUnitSubscriptionHasNotBeenUpdatedException::class,
        803  => Response\TheUnitSubscriptionHasNotBeenDeletedException::class,
        811  => Response\ThePeriodSubscriptionHasNotBeenAddedException::class,
        812  => Response\ThePeriodSubscriptionHasNotBeenUpdatedException::class,
        813  => Response\ThePeriodSubscriptionHasNotBeenDeletedException::class,
        821  => Response\TheSeriesSubscriptionHasNotBeenAddedException::class,
        822  => Response\TheSeriesSubscriptionHasNotBeenUpdatedException::class,
        823  => Response\TheSeriesSubscriptionHasNotBeenDeletedException::class,
        901  => Response\TheCharacterStringToBeSearchedForCannotBeBlankException::class,
        902  => Response\TheSubscriberLoginCannotBeBlankException::class,
        903  => Response\ThisSubscriberLoginAlreadyExistsException::class,
        904  => Response\UnknownUnitSubscriberForThisPublicationException::class,
        905  => Response\UnknownSubscriptionPeriodException::class,
        906  => Response\UnknownSubscriptionSeriesException::class,
        907  => Response\ThePublicationCirculationModeCannotBePublicException::class,
        1710 => Response\DocumentDeniedException::class,
        1711 => Response\IdenticalDocumentAlreadyExistException::class,
        1712 => Response\RequestDeniedException::class,
        1713 => Response\RequestDeniedException::class,
        1714 => Response\TooManyPublicationsAlreadyConvertingException::class,
        1715 => Response\MaximumNumberOfPrivatePublicationsReachedException::class,
        1720 => Response\DocumentSizeExceedsMaximumAllowedSizeException::class,
        1721 => Response\MaximumStorageCapacityReachedException::class,
        1722 => Response\UnsupportedDocumentTypeException::class,
        1723 => Response\DocumentSizeExceedsMaximumAllowedSizeException::class,
        1724 => Response\DocumentSizeExceedsMaximumAllowedSizeException::class,
        1725 => Response\DocumentPartiallyUploadedException::class,
        1726 => Response\DocumentNotUploadedException::class,
        1727 => Response\InternalErrorException::class,
        1728 => Response\InternalErrorException::class,
    ];

    /**
     * @param int    $code
     * @param string $message
     *
     * @throws ApiResponseErrorException
     */
    public function throwApiException(int $code, string $message)
    {
        $exceptionClass = $this->exceptionsByCode[$code] ?? Response\ApiResponseException::class;
        throw new $exceptionClass($message, $code);
    }
}
