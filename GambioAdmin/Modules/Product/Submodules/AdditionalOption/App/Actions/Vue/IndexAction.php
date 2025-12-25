<?php
/*------------------------------------------------------------------------------
 IndexAction.php 2023-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Vue;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response as HttpResponse;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;

class IndexAction extends VuePageAction
{
    private const TRANSLATION_SECTION = 'options';
    
    private const TRANSLATIONS = [
        'add_option',
        'create_option',
        'attach_options_button',
        'attach_options',
        'attach_options_description',
        'cancel',
        'create',
        'create_attach',
        'attach_save',
        'add_existing_option',
        'add_existing_value',
        'delete',
        'close',
        'save',
        'fill_all_fields',
        'label',
        'label_description',
        'description',
        'description_description',
        'edit_option_heading',
        'sort_option_heading',
        'add_option_heading',
        'add_option_admin_label',
        'add_option_admin_label_description',
        'add_option_type',
        'add_option_type_description',
        'add_option_type_option_dropdown',
        'add_option_type_option_image',
        'add_option_type_option_radio',
        'add_option_type_option_text',
        'add_option_type_option_boxed_text',
        'add_option_type_preview',
        'confirm_modal_heading',
        'confirm_modal_body',
        'edit_value_heading',
        'edit_option_value_modal_title',
        
        'values_table_row_actions',
        'values_table_col_value',
        'values_table_col_model',
        'values_table_col_price',
        'values_table_col_gross_price',
        'values_table_col_net_price',
        'values_table_col_weight',
        'values_table_col_stock',
        'values_table_no_values_added',
        'values_table_add_value',
        
        'value_modal_add_heading',
        'value_modal_edit_heading',
        'value_modal_sort_heading',
        'value_modal_label',
        'value_modal_label_description',
        'value_modal_description',
        'value_modal_description_description',
        'value_modal_image',
        'value_modal_image_description',
        'value_modal_image_select',
        'value_modal_image_no_file_selected',
        'value_modal_image_selected',
        'value_modal_model_number',
        'value_modal_model_number_description',
        'value_modal_weight',
        'value_modal_weight_description',
        'value_modal_gross_price',
        'value_modal_net_price',
        'value_modal_price_description',
        'value_modal_stock',
        'value_modal_stock_description',
        
        'edit_image',
        'add_image',
        'edit_image_collection',
        'add_image_collection',
        'new_collection',
        'select_collection',
        'collection_name',
        'add_images',
        'main_image',
        
        'file_manager_modal_title',
        
        'attach_options_modal_title',
        'attach_options_modal_label',
        'attach_options_modal_description',
        'attach_options_modal_available_product_options',
        'attach_options_modal_selected_options',
        'attach_options_modal_search_options',
        'attach_options_modal_search_not_found',
        'attach_options_modal_no_available_options',
        'attach_options_modal_attached_product_options',
        'attach_options_modal_attached_product_options_number',
        'attach_options_modal_attached_no_options',
        'attach_options_modal_attached_no_options_tip',
        
        'attach_option_values_modal_title_add',
        'attach_option_values_modal_title_attach',
        'attach_option_values_label',
        'attach_option_values_description',
        'attach_option_values_available_option_values',
        'attach_option_values_selected_values',
        'attach_option_values_search_values',
        'attach_option_values_search_not_found',
        'attach_option_values_attached_option_values',
        'attach_option_values_attached_option_values_number',
        'attach_option_values_attached_no_values',
        'attach_option_values_attached_no_values_tip',
        'attach_option_values_no_values_to_attach',
        
        'success_heading',
        'success_message',
    ];

    
    /**
     * ActionIndex constructor.
     *
     * @param Connection          $connection
     * @param UserPreferences     $userPreferences
     * @param AdminMenuService    $adminMenuService
     * @param LanguageService     $languageService
     * @param ConfigurationFinder $configurationFinder
     */
    public function __construct(
        private Connection          $connection,
        private UserPreferences     $userPreferences,
        AdminMenuService            $adminMenuService,
        private LanguageService     $languageService,
        private ConfigurationFinder $configurationFinder
    ) {
        // Workaround to keep the Admin Menu "Catalog > Categories/Products" active
        $adminMenuService->changeSelectedAdminPage('categories.php');
    }
    
    
    /**
     * @inheritDoc
     * @throws RenderingFailedException
     * @throws LanguageNotFoundException
     * @throws Exception
     */
    public function handle(Request $request, HttpResponse $response): HttpResponse
    {
        $productId = (int)$request->getAttribute('productId');
        
        $title    = $this->getProductNameById($productId);
        $template = dirname(__DIR__, 3) . '/ui/productOptions.html';
        
        foreach (self::TRANSLATIONS as $translation) {
            $this->addVuePageTranslation($translation, self::TRANSLATION_SECTION);
        }
        
        $data = [
            'product'            => [
                'id'         => $productId,
                'name'       => $title,
                'specialsId' => $this->getProductSpecialId($productId),
            ],
            'cPath'              => $request->getParam('cPath'),
            'currency'           => $_SESSION['currency'] ?? 'EUR',
            'isGrossAdminActive' => $this->configurationFinder->get('configuration/PRICE_IS_BRUTTO', 'true'),
            'language'           => json_encode([
                                                    'activeLanguage' => $this->languageService->getLanguageById($this->userPreferences->languageId())
                                                        ->code(),
                                                    'languages'      => $this->languageService->getAvailableAdminLanguages()
                                                        ->toArray(),
                                                ]),
        ];
        
        return $response->write($this->render($title, $template, $data));
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return 'product__additional_option';
    }
    
    
    /**
     * @param int $productId
     *
     * @return string
     * @throws Exception
     * @todo Attention: Remove this method once we have a service for getting the Product from the DB
     *
     */
    private function getProductNameById(int $productId): string
    {
        $product = $this->connection->createQueryBuilder()
            ->select('pd.products_name')
            ->from('products_description', 'pd')
            ->innerJoin('pd', 'products', 'p', 'p.products_id = pd.products_id')
            ->where('pd.products_id = :productId AND pd.language_id = :languageId')
            ->setParameters(['productId' => $productId, 'languageId' => $this->userPreferences->languageId()])
            ->executeQuery()
            ->fetchAssociative();
        
        return $product['products_name'] ?? '';
    }


    /**
     * @param int $productId
     *
     * @return int|null
     * @throws Exception
     * @todo: Remove this method once we have a service for getting the Product spcials ID from the DB
     */
    private function getProductSpecialId(int $productId): ?int
    {
        $specialId = $this->connection->createQueryBuilder()
            ->select('specials_id')
            ->from('specials')
            ->where('products_id = :productId')
            ->setParameters([
                                'productId' => $productId])
                            ->executeQuery()
            ->fetchAssociative();

        return $specialId ? (int)$specialId['specials_id'] : null;
    }
}
