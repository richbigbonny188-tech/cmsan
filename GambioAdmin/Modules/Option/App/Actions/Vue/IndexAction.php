<?php
/*------------------------------------------------------------------------------
  ActionIndex.php 2021-10-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Actions\Vue;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response as HttpResponse;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class ActionIndex
 *
 * @package Gambio\Admin\Modules\Option\App\Actions
 * @codeCoverageIgnore
 */
class IndexAction extends VuePageAction
{
    private const TRANSLATION_SECTION = 'options';
    
    private const TRANSLATIONS = [
        'add_option',
        'create_option',
        'create_option_description',
        'cancel',
        'create',
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
        
        'values_table_row_actions',
        'values_table_col_value',
        'values_table_col_model',
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
        
        'file_manager_modal_title',
        
        'success_heading',
        'success_message',
        
        'error_heading',
        'error_message',
        'error_message_409',
    ];
    
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    /**
     * @var LanguageService
     */
    private $languageService;
    
    /**
     * @var ConfigurationFinder
     */
    private $configurationFinder;
    
    
    /**
     * @param UserPreferences     $userPreferences
     * @param LanguageService     $languageService
     * @param TextManager         $textManager
     * @param ConfigurationFinder $configurationFinder
     */
    public function __construct(
        UserPreferences     $userPreferences,
        LanguageService     $languageService,
        TextManager         $textManager,
        ConfigurationFinder $configurationFinder
    ) {
        $this->userPreferences     = $userPreferences;
        $this->languageService     = $languageService;
        $this->textManager         = $textManager;
        $this->configurationFinder = $configurationFinder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, HttpResponse $response): HttpResponse
    {
        $title = $this->textManager->getPhraseText('heading_title',
                                                   self::TRANSLATION_SECTION,
                                                   $this->userPreferences->languageId());
        
        $template = dirname(__DIR__, 3) . '/ui/options.html';
        
        foreach (self::TRANSLATIONS as $translation) {
            $this->addVuePageTranslation($translation, self::TRANSLATION_SECTION);
        }
        
        return $response->write($this->render($title,
                                              $template,
                                              [
                                                  'language'           => json_encode([
                                                                                          'activeLanguage' => $this->languageService->getLanguageById($this->userPreferences->languageId())
                                                                                              ->code(),
                                                                                          'languages'      => $this->languageService->getAvailableAdminLanguages()
                                                                                              ->toArray(),
                                                                                      ]),
                                                  'currency'           => $_SESSION['currency'] ?? 'EUR',
                                                  'isGrossAdminActive' => $this->configurationFinder->get('configuration/PRICE_IS_BRUTTO',
                                                                                                          'true'),
                                              ]));
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return 'option';
    }
}
