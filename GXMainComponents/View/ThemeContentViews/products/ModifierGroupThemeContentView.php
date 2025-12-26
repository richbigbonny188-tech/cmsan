<?php
/*--------------------------------------------------------------------------------------------------
    ModifierGroupThemeContentView.php 2023-06-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Shop\Attributes\ProductModifiers\Database\AttributeModifier;
use Gambio\Shop\Attributes\ProductModifiers\Database\Interfaces\AttributeModifierInterface;
use Gambio\Shop\Price\Product\Database\ValueObjects\CustomersStatusShowPrice;
use Gambio\Shop\ProductModifiers\Groups\GroupInterface;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ModifierInterface;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\ImageType;
use Gambio\Shop\Properties\ProductModifiers\Database\PropertyModifier;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;

/**
 * Class ModifierGroupThemeContentView
 */
class ModifierGroupThemeContentView extends ThemeContentView
{
    /**
     * @var GroupInterface
     */
    protected $group;
    /**
     * @var ModifierIdentifierCollectionInterface
     */
    protected $selected_modifier_ids;
    /**
     * @var SellingUnitInterface
     */
    protected $sellingUnit;
    /**
     * @var CustomersStatusShowPrice
     */
    protected $showPriceStatus;
    /**
     * @var TextManager
     */
    protected $textManager;
    
    
    /**
     * ModifierGroupThemeContentView constructor.
     *
     * @param SellingUnitInterface     $sellingUnit
     * @param CustomersStatusShowPrice $showPriceStatus
     */
    public function __construct(SellingUnitInterface $sellingUnit, CustomersStatusShowPrice $showPriceStatus)
    {
        parent::__construct(false, false);
        $this->sellingUnit = $sellingUnit;
        $this->showPriceStatus = $showPriceStatus;
    }
    
    
    /**
     * @param GroupInterface $group
     */
    public function set_group(GroupInterface $group)
    {
        $this->group = $group;
    }


    /**
     * @param ModifierIdentifierCollectionInterface $selected_modifier_ids
     */
    public function set_selected_modifier_ids(ModifierIdentifierCollectionInterface $selected_modifier_ids): void
    {
        $this->selected_modifier_ids = $selected_modifier_ids;
    }

    /**
     * @throws Exception
     */
    public function prepare_data()
    {
        $filepath   = DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getProductOptionsTemplatePath();
        /*
         * Types are defined at src/GXMainComponents/Services/Core/ProductModifierDisplayTypes/ValueObjects
         */
        $templateName = strtolower($this->group->type()::type());
        $c_template = $this->get_default_template($filepath, 'modifier_group_type_', "{$templateName}.html");
        $this->set_content_template($c_template);

        $this->set_content_data('modifier', $this->parse_content_data());
        $this->set_content_data('show_additional_price', $this->showAdditionalPrice());
        

        parent::prepare_data();
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function parse_content_data()
    {
        $parsedModifiers = $this->parse_modifiers();

        return [
            'id'       => "modifier_group_{$this->group->id()->id()}",
            'name'     => "modifiers[{$this->group->id()->type()}][{$this->group->id()->id()}]",
            'type'     => $this->group->id()->type(),
            'label'    => $this->group->name()->value(),
            'items'    => $parsedModifiers['items'],
            'selected' => $parsedModifiers['selected'],
            'visible'  => $this->group->status()->isSelectable(),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function parse_modifiers()
    {
        $parsedData = [
            'items' => [],
            'selected' => []
        ];

        /**
         * @var ModifierInterface $modifier
         */
        foreach ($this->group->modifiers() as $key => $modifier) {


            $data = [
                'id'             => $modifier::source() . '_' . $modifier->id()->value(),
                'value'          => $modifier->id()->value(),
                'selectable'     => $modifier->selectable()->isSelectable(),
                'info'           => $modifier->info()->label()->value(),
                'additionalInfo' => [
                    'price' => $this->calculateAdditionalPriceInfo($modifier),
                    'stock' => $this->getStockInformation($modifier)
                ],
                'label'          => $modifier->name()->name()
            ];

            if ($modifier->selected()->isSelected()) {

                $parsedData['selected'] = [
                    'id'             => $data['id'],
                    'value'          => $data['value'],
                    'info'           => $data['info'],
                    'label'          => $data['label'],
                    'additionalInfo' => $data['additionalInfo']
                ];
            }


            if ($this->group->type()::type() === ImageType::type()) {
                $data['path'] = $modifier->info()->path()->public();
            }

            $parsedData['items'][] = $data;
        }

        return $parsedData;
    }

    /**
     * @param ModifierInterface $modifier
     * @return string
     */
    protected function calculateAdditionalPriceInfo(ModifierInterface $modifier): string
    {
        $additionalPrice = $modifier->additionalInfo()->price();
    
        if ($additionalPrice && $this->showPriceStatus->value() && $this->validatePriceStatus()) {
        
            $xtcPrice = $this->sellingUnit->xtcPrice();
            
            if ($additionalPrice > 0) {

                //  Property price already has the taxes included in its price, if admin enters prices as gross
                $taxClassId = $modifier instanceof AttributeModifier
                              || PRICE_IS_BRUTTO !== 'true' ? $this->sellingUnit->taxInfo()->taxClassId() : 0;
                
                // Remove tax from variant option price if admin enters prices as gross
                if ($modifier instanceof PropertyModifier && PRICE_IS_BRUTTO === 'true') {
                    $additionalPrice = $xtcPrice->xtcRemoveTax($additionalPrice,
                                                               xtc_get_tax_rate($this->sellingUnit->taxInfo()
                                                                                    ->taxClassId(),
                                                                                (int)STORE_COUNTRY,
                                                                                (int)STORE_ZONE,
                                                                                0));
                    
                    // add the tax considering the country of the current user
                    if ($xtcPrice->cStatus['customers_status_show_price_tax'] === '1') {
                        $additionalPrice *= (1 + xtc_get_tax_rate($this->sellingUnit->taxInfo()->taxClassId()) / 100);
                    }
                }
    
                if ($xtcPrice->cStatus['customers_status_discount_attributes'] == 1 &&
                    $discountAllowed = $this->sellingUnit->productInfo()->discountAllowed()->value()
                ) {
                    $additionalPrice -= $additionalPrice / 100 * $discountAllowed;
                }
                
                $priceFormatted = $xtcPrice->xtcFormat($additionalPrice, true, $taxClassId, true, true)['formated'];
    
                return $modifier->additionalInfo()->pricePrefix() . ' ' . $priceFormatted;
            }
        }
        
        return '';
    }
    
    
    /**
     * @return bool
     */
    protected function validatePriceStatus(): bool
    {
        $productId = $this->sellingUnit->id()->productId()->value();
        $xtcPrice  = $this->sellingUnit->xtcPrice();
        
        $checkPriceStatusResult = (int)$xtcPrice->gm_check_price_status($productId);
    
        return $checkPriceStatusResult === 0
               || ($checkPriceStatusResult === 2 && $xtcPrice->getPprice($productId) > 0);
    }
    
    
    /**
     * @return bool
     */
    protected function showAdditionalPrice(): bool
    {
        if ($this->group->id()->type() !== 'property') {
            
            return true;
        }
        
        return $this->sellingUnit->productInfo()->showAdditionalPriceInformation()->value();
    }
    
    
    /**
     * @param ModifierInterface $modifier
     *
     * @return string
     */
    protected function getStockInformation(ModifierInterface$modifier): string
    {
        $additionalModifierStockInformation = '';
        if ($modifier->additionalInfo()->showStock()) {
            $stockQuantity = $this->getModifierStock($modifier);
            $availableText = $this->getTextManagerService()->getPhraseText('available', 'properties_dropdown');
        
            $additionalModifierStockInformation = " [{$availableText}: {$stockQuantity}]";
        }
    
        return $additionalModifierStockInformation;
    }
    
    
    /**
     * @param ModifierInterface $modifier
     *
     * @return int
     */
    protected function getModifierStock(ModifierInterface $modifier): int
    {
        if (!$modifier instanceof AttributeModifierInterface) {
            return 0;
        }
    
        // First tries to get the information from the selected values (SellingUnit)
        // If it doesn't find, get from the database
        $stock = $this->sellingUnit->stock()->availableQuantity()->byModifier($modifier->id());
        if ($stock) {
            return $stock->value();
        }
        
        $queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $result = $queryBuilder->select('po.products_options_id, pov.products_options_values_id, pa.attributes_stock')
            ->from('products_attributes pa')
            ->join('products_options po', 'po.products_options_id = pa.options_id')
            ->join('products_options_values_to_products_options povtpo', 'po.products_options_id = povtpo.products_options_id AND povtpo.products_options_values_id = pa.options_values_id')
            ->join('products_options_values pov', 'povtpo.products_options_values_id = pov.products_options_values_id AND pov.language_id = po.language_id')
            ->where('pa.products_id', $this->sellingUnit->id()->productId()->value())
            ->where('po.language_id', $_SESSION['languages_id'])
            ->where('pov.products_options_values_id', $modifier->id()->value())
            ->group_by('po.products_options_id, pov.products_options_values_id, pa.attributes_stock')
            ->get()
            ->result_array();
    
        $result = reset($result);
        $stock = $result['attributes_stock'] ?? 0;
    
        return (int)$stock;
    }
    
    
    /**
     * @return TextManager
     */
    protected function getTextManagerService(): TextManager
    {
        if ($this->textManager === null) {
            $this->textManager = LegacyDependencyContainer::getInstance()->get(TextManager::class);
        }
    
        return $this->textManager;
    }
}

