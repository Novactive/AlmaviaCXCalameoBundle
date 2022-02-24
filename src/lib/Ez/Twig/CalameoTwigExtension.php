<?php

/**
 * @copyright Novactive
 * Date: 24/02/2022
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Ez\Twig;

use AlmaviaCX\Calameo\API\Value\Publication;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Value;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CalameoTwigExtension extends AbstractExtension
{
    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     */
    public function __construct(NotificationHandlerInterface $notificationHandler)
    {
        $this->notificationHandler = $notificationHandler;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('loadCalameoPublication', [$this, 'loadCalameoPublication']),
        ];
    }

    public function loadCalameoPublication(Value $value): ?Publication
    {
        try {
            return $value->publication;
        } catch (ApiResponseErrorException $exception) {
            $this->notificationHandler->error(
                sprintf("[Calameo] %s", $exception->getMessage())
            );
        }
        return null;
    }
}
