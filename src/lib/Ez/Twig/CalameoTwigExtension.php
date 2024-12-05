<?php

/**
 * @copyright Novactive
 * Date: 24/02/2022
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Ez\Twig;

use AlmaviaCX\Calameo\API\Repository\PublicationRepository;
use AlmaviaCX\Calameo\API\Value\Publication;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Exception\Response\UnknownBookIDException;
use AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Value;
use Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CalameoTwigExtension extends AbstractExtension
{
    protected NotificationHandlerInterface $notificationHandler;
    protected PublicationRepository $publicationRepository;

    public function __construct(
        NotificationHandlerInterface   $notificationHandler,
        PublicationRepository $publicationRepository
    ) {
        $this->publicationRepository = $publicationRepository;
        $this->notificationHandler = $notificationHandler;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('loadCalameoPublication', [$this, 'loadCalameoPublication']),
        ];
    }

    public function loadCalameoPublication(Value $value): ?Publication
    {
        if ($value->folderId && $value->publicationId) {
            try {
                return $this->publicationRepository->getPublicationInfos($value->publicationId);
            } catch (UnknownBookIDException $unknownBookIDException) {
                $this->notificationHandler->warning(
                    sprintf("[Calameo] %s", $unknownBookIDException->getMessage()) // Unknown book
                );
            } catch (ApiResponseErrorException $exception) {
                $this->notificationHandler->error(
                    sprintf("[Calameo] %s", $exception->getMessage())
                );
            }
        }
        return null;
    }
}
