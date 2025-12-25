<?php
/*
 * --------------------------------------------------------------
 *   GambioOmnibusPolicyModuleCenterModuleController.inc.php 2022-05-27
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

class GambioOmnibusPolicyModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    private const MODULE_PATH             = 'GXModules/Gambio/OmnibusPolicy/';
    private const MODULE_TEMPLATE_CONFIGS = 'Admin/Html/omnibus_policy_configuration.html';
    private const MODULE_STYLES_PATH      = '../GXModules/Gambio/OmnibusPolicy/Admin/Styles/';
    /**
     * @var GambioOmnibusPolicyConfigurationStorage
     */
    private $configuration;
    /**
     * @var GambioOmnibusPolicyTextPhrasesStorage
     */
    private $textPhrases;


    /**
     * @inheritDoc
     */
    protected function _init()
    {
        $this->configuration = MainFactory::create('GambioOmnibusPolicyConfigurationStorage');
        $this->textPhrases   = MainFactory::create('GambioOmnibusPolicyTextPhrasesStorage');
        $this->pageTitle     = $this->languageTextManager->get_text(
            'configuration_page_title',
            'omnibus_policy'
        );
    }


    /**
     * Return the default page
     *
     * @return AdminLayoutHttpControllerResponse Layout response
     * @throws Exception
     */
    public function actionDefault()
    {
        $title    = new NonEmptyStringType($this->pageTitle);
        $template = $this->getTemplateFile(
            self::MODULE_PATH . self::MODULE_TEMPLATE_CONFIGS
        );

        $data = MainFactory::create(
            'KeyValueCollection',
            [
                'activeLanguageCode'        => strtolower($_SESSION['language_code']),
                'activeLanguageId'          => strtolower($_SESSION['languages_id']),
                'pageToken'                 => $_SESSION['coo_page_token']->generate_token(),
                'configuration'             => $this->configuration->getAll(),
                'text_phrases'              => $this->textPhrases->getAll(),
                'action_back_to_modules'    => xtc_href_link(
                    'admin.php',
                    'do=ModuleCenter'
                ),
                'action_save_configuration' => xtc_href_link(
                    'admin.php',
                    'do=GambioOmnibusPolicyModuleCenterModule/Save'
                ),
            ]
        );

        $assets = $this->getTemplateAssets('verifiedReviews.css');

        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }


    /**
     * Save the configuration values
     *
     * @return RedirectHttpControllerResponse Redirect response
     * @throws Exception
     */
    public function actionSave()
    {
        $this->_validatePageToken();

        $configuration           = $this->_getPostData('configuration');
        $textPhrases             = $this->_getPostData('text_phrases');
        $configuration           = $configuration ? (array)$configuration : [];
        $textPhrases             = $textPhrases ? (array)$textPhrases : [];
        $configurationSaveAction = $this->saveData($configuration, $this->configuration);
        $textPhrasesSaveAction   = $this->saveData($textPhrases, $this->textPhrases);

        $failed = !$configurationSaveAction || !$textPhrasesSaveAction;

        if ($failed) {
            $GLOBALS['messageStack']->add_session(
                $this->languageTextManager->get_text('configuration_saving_error', 'omnibus_policy'),
                'error'
            );
        } else {
            $GLOBALS['messageStack']->add_session(
                $this->languageTextManager->get_text('configuration_saving_saved', 'omnibus_policy'),
                'success'
            );
        }

        return MainFactory::create(
            'RedirectHttpControllerResponse',
            xtc_href_link(
                'admin.php',
                'do=GambioOmnibusPolicyModuleCenterModule'
            )
        );
    }


    /**
     * @param string $filePath
     *
     * @return AssetCollection
     */
    private function getTemplateAssets($filePath)
    {
        $assets = new AssetCollection();
        $assets->add(new Asset(self::MODULE_STYLES_PATH . $filePath));

        return $assets;
    }


    /**
     * @param array                $data
     * @param ConfigurationStorage $storage
     *
     * @return bool
     * @throws Exception
     */
    private function saveData($data, $storage)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid data provided');
        }

        if (!$storage instanceof ConfigurationStorage || !method_exists($storage, 'setAll')) {
            throw new InvalidArgumentException('Invalid storage provided');
        }

        $errors = false;

        try {
            $storage->setAll($data);
        } catch (Exception $exception) {
            $errors = true;
        }

        return false === $errors;
    }
}
