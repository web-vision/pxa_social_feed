<?php

namespace Pixelant\PxaSocialFeed\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BackendUserGroupRepository
 * @package Pixelant\PxaSocialFeed\Domain\Repository
 */
class BackendUserGroupRepository
{
    /**
     * Find all BE user groups
     *
     * @param array $exclude Uids of groups to exclude
     * @return array
     */
    public function findAll($exclude = null)
    {
        return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            '*',
            'be_groups',
            empty($exclude) ? '' : sprintf('uid NOT IN (%s)', implode(',', $exclude))
        );
    }
}
