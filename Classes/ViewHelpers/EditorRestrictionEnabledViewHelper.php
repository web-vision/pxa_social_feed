<?php

namespace Pixelant\PxaSocialFeed\ViewHelpers;

use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Evaluate condition if restriction is enabled for editors
 *
 * @package Pixelant\PxaSocialFeed\ViewHelpers
 */
class EditorRestrictionEnabledViewHelper extends AbstractConditionViewHelper
{
    /**
     * Check if feature enable in plugin settings
     *
     * @param array $arguments
     * @return bool
     */
    public static function evaluateCondition($arguments = null)
    {
        return ConfigurationUtility::isFeatureEnabled('editorRestriction');
    }
}
