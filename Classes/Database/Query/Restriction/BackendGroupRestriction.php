<?php

namespace Pixelant\PxaSocialFeed\Database\Query\Restriction;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Restrict access by BE user group
 */
class BackendGroupRestriction
{
    /**
     * @var string
     */
    protected $groupFieldName = 'be_group';

    /**
     * @var BackendUserAuthentication|null
     */
    protected $backendUserAuth = null;

    /**
     * @var string
     */
    protected $where = '';

    /**
     * Initialize
     *
     * @param $tableName
     */
    public function __construct($tableName)
    {
        if (isset($GLOBALS['BE_USER'])) {
            $this->backendUserAuth = $GLOBALS['BE_USER'];
        }

        $this->where = $this->buildAdditionalWhere($tableName);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->where;
    }

    /**
     * Build BE groups restriction
     *
     * @param string $tableName
     * @return string Additional where clause
     */
    protected function buildAdditionalWhere($tableName)
    {
        $constraints = [];
        if ($this->backendUserAuth !== null && !$this->backendUserAuth->isAdmin()) {
            $fieldName = $tableName . '.' . $this->groupFieldName;
            // Allow records where no group access has been configured (field values NULL, 0 or empty string)
            $constraints = [
                "$fieldName is NULL",
                "$fieldName = ''",
                "$fieldName = '0'",
            ];

            $backendGroupIds = GeneralUtility::intExplode(',', $this->backendUserAuth->groupList);
            foreach ($backendGroupIds as $backendGroupId) {
                $constraints[] = "FIND_IN_SET($backendGroupId, $fieldName)";
            }
        }
        if (!empty($constraints)) {
            return sprintf('(%s)', implode(' OR ', $constraints));
        }

        return '';
    }
}
