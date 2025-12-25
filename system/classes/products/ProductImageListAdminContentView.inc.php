<?php
/* --------------------------------------------------------------
   ProductImageListAdminContentView.inc.php 2020-02-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Shop\Attributes\ProductModifiers\Database\AttributeModifier;

/**
 * Class ProductImageListAdminContentView
 */
class ProductImageListAdminContentView extends LightboxContentView
{

    /**
     * @var bool|LanguageProvider|mixed
     */
    protected $languageProvider;

    public function __construct()
    {
        parent::__construct();
        $this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/product_image_list/');
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->languageProvider = MainFactory::create('LanguageProvider', $db);

    }

    public function prepare_data()
    {
        $this->set_content_data("admin_languages", $this->getAdminLanguages());
    }

    /**
     * @param array $p_get_array
     * @param array $p_post_array
     * @return mixed|void
     */
    public function get_html_array($p_get_array = [], $p_post_array = [])
    {
//        $this->set_lightbox_button('right', 'save', ['save', 'green']);
        $this->set_lightbox_button('right', 'save_close', ['save_close', 'green', 'btn-primary']);
        $this->set_lightbox_button('left', 'cancel', ['close', 'lightbox_close']);

        $this->set_content_data('product_image_list_id', $p_get_array['product_image_list_id']);
        $this->set_content_data('currentParentId', $p_get_array['products_options_values_id']);
        $this->set_content_data('modifierId', $p_get_array['modifierId']);
        $this->set_content_data('modifierType', AttributeModifier::source());

        $fileManagerConfiguration = MainFactory::create('ResponsiveFileManagerConfigurationStorage');
        $useFileManager = $fileManagerConfiguration->isInstalled() && $fileManagerConfiguration->get('use_in_attribute_pages');
        $this->set_content_data('useFileManager', $useFileManager);

        $t_html_output['html'] = $this->build_html();

        return $t_html_output;
    }

    /**
     * Gets all admin languages, even if they're inactive
     *
     * @return array
     */
    protected function getAdminLanguages()
    {
        foreach ($this->languageProvider->getAdminCodes()->getArray() as $adminLanguage) {
            $languages[] = [
                'code' => $adminLanguage->asString(),
                'icon' => $this->languageProvider->getIconFilenameByCode($adminLanguage),
                'id'   => $this->languageProvider->getIdByCode($adminLanguage)
            ];
        }

        return $languages ?? [];
    }
}