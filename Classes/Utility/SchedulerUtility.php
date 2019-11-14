<?php

namespace Pixelant\PxaSocialFeed\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @package Pixelant\PxaSocialFeed\Utility
 */
class SchedulerUtility
{
    /**
     * Generate html box
     *
     * @param array $selectedConfigurations
     * @return string
     */
    public static function getAvailableConfigurationsSelectBox(array $selectedConfigurations)
    {
        $selector = '<select class="form-control" name="tx_scheduler[pxasocialfeed_configs][]" multiple>';

        $statement = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'uid, name',
            'tx_pxasocialfeed_domain_model_configuration',
            ''
        );

        if ($statement !== false) {
            while ($config = $statement->fetch_assoc()) {
                $selectedAttribute = '';
                if (is_array($selectedConfigurations) && in_array($config['uid'], $selectedConfigurations)) {
                    $selectedAttribute = ' selected="selected"';
                }

                $selector .= sprintf(
                    '<option value="%d"%s>%s</option>',
                    $config['uid'],
                    $selectedAttribute,
                    $config['name']
                );
            }
        }

        $selector .= '</select>';

        return $selector;
    }

    /**
     * @param array $configurations
     * @param bool $runAllConfigurations
     * @return string
     */
    public static function getSelectedConfigurationsInfo($configurations, $runAllConfigurations)
    {
        if ($runAllConfigurations) {
            return 'Feeds: All configurations';
        }

        if (!empty($configurations)) {
            $statement = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'uid, name',
                'tx_pxasocialfeed_domain_model_configuration',
                sprintf('uid IN (%s)', implode(',', $configurations))
            );
        } else {
            $statement = false;
        }


        $info = 'Feeds: ';
        if ($statement === false) {
            return $info;
        }

        while ($config = $statement->fetch_assoc()) {
            $info .= $config['name'] . ' [UID: ' . $config['uid'] . ']; ';
        }

        return $info;
    }
}
