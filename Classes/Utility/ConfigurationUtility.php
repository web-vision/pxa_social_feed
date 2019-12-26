<?php

namespace Pixelant\PxaSocialFeed\Utility;

/**
 * Read plugin configuration
 *
 * @package Pixelant\PxaSocialFeed\Utility
 */
class ConfigurationUtility
{
    /**
     * Get extension configuration
     *
     * @return array
     */
    public static function getExtensionConfiguration()
    {
        $settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pxa_social_feed']);

        if (!is_array($settings)) {
            $settings = [];
        }

        return $settings;
    }

    /**
     * Check if feature is enabled in configuration
     *
     * @param string $feature
     * @return bool
     */
    public static function isFeatureEnabled($feature)
    {
        $settings = static::getExtensionConfiguration();
        if (isset($settings[$feature])) {
            return (bool)$settings[$feature];
        }

        return false;
    }
}
