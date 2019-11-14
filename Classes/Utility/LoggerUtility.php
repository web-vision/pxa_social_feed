<?php

namespace Pixelant\PxaSocialFeed\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Class LoggerUtility
 * @package Pixelant\PxaSocialFeed\Utility
 */
class LoggerUtility
{
    /**
     * Message type
     */
    const INFO = 0;
    const ERROR = 1;

    /**
     * Log error
     *
     * @param string $message
     * @param int $type
     */
    public static function log($message, $type = self::INFO)
    {
        if (!($GLOBALS['BE_USER'] instanceof BackendUserAuthentication)) {
            return;
        }

        /** @var BackendUserAuthentication $beUser */
        $beUser = $GLOBALS['BE_USER'];
        $beUser->writelog(
            4,
            0,
            $type,
            0,
            '[pxa_social_feed] ' . $message,
            []
        );
    }
}
