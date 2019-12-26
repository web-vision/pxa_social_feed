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
     * @return array
     */
    public function findAll()
    {
        return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            '*',
            'be_groups',
            ''
        );
    }
}
