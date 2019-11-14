<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Migrate tables data
 */
class ext_update
{
    /**
     * Check if script could run
     */
    public function access()
    {
        return $this->legacyFieldsExist();
    }

    /**
     * Main update function
     */
    public function main()
    {
        $url = GeneralUtility::getIndpEnv('REQUEST_URI');
        $performMigration = (bool)GeneralUtility::_GET('performMigration');

        if ($performMigration) {
            $this->migrate();
        }

        $fluidTemplate = $this->getTemplate();
        $view = GeneralUtility::makeInstance(ObjectManager::class)->get(StandaloneView::class);
        $view->setTemplateSource($fluidTemplate);

        $view->assignMultiple(
            [
                'url' => $url,
                'performMigration' => $performMigration
            ]
        );

        return $view->render();
    }

    /**
     * Migrate script
     */
    protected function migrate()
    {
        $this->migrateConfiguration();
        $this->migrateTokens();
    }

    /**
     * Migrate tokens fields
     */
    protected function migrateTokens()
    {
        $rows = $this->getDB()->exec_SELECTgetRows(
            'uid, serialized_credentials, social_type',
            'tx_pxasocialfeed_domain_model_token',
            ''
        );
        // Do not migrate instagram access, it doesn't work anymore
        $oldToNewFields = [
            'appId' => 'app_id',
            'appSecret' => 'app_secret',
            'accessToken' => 'access_token',
            'apiKey' => 'api_key',
            'consumerKey' => 'api_key',
            'consumerSecret' => 'api_secret_key',
            'accessTokenSecret' => 'access_token_secret',
        ];

        foreach ($rows as $row) {
            $credentials = unserialize($row['serialized_credentials']);
            $updateFields = [
                'type' => $row['social_type'],
            ];

            foreach ($credentials as $credential => $credentialValue) {
                if (array_key_exists($credential, $oldToNewFields)) {
                    $newField = $oldToNewFields[$credential];
                    $updateFields[$newField] = $credentialValue;
                }
            }

            $this->getDB()->exec_UPDATEquery(
                'tx_pxasocialfeed_domain_model_token',
                'uid=' . $row['uid'],
                $updateFields
            );
        }
    }

    /**
     * Migrate fields configuration
     */
    protected function migrateConfiguration()
    {
        $query = 'UPDATE tx_pxasocialfeed_domain_model_configuration SET';
        $query .= ' max_items=feeds_limit, storage=feed_storage';

        $this->getDB()->sql_query($query);
    }

    /**
     * Check if old extension fields exist
     */
    protected function legacyFieldsExist()
    {
        $fields = $this->getDB()->admin_get_fields('tx_pxasocialfeed_domain_model_token');

        return array_key_exists('serialized_credentials', $fields);
    }

    /**
     * Fluid template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return <<<FLUID
<f:if condition="{performMigration}">
    <f:then>
        <p class="bg-success" style="padding: 15px;">DB fields were migrate. Run DB analysis now in upgrade wizard.</p>
    </f:then>
    <f:else>
        <h1>Migration of DB fields</h1>
        <p><a class="btn btn-success" href="{url}&performMigration=1">Perform migration</a></p>
        <p><em>Performing the migration will migrate data from old DB fields to new</em></p>
    </f:else>
</f:if>
FLUID;
    }

    /**
     * @return mixed|\TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDB()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}