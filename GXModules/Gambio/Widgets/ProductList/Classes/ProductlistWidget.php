<?php
/*--------------------------------------------------------------------------------------------------
    ProductListWidget.php 2023-11-30
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use Gambio\StyleEdit\Core\Components\Checkbox\Entities\CheckboxOption;
use Gambio\StyleEdit\Core\Components\DropdownSelect\Entities\DropdownSelectOption;
use Gambio\StyleEdit\Core\Components\ProductListGroup\Entities\ProductListGroupOption;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Options\Entities\FieldSet;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;

/**
 * Class representing a product list widget
 */
class ProductlistWidget extends AbstractWidget
{
    
    /**
     * HTML element class
     *
     * @var TextBox
     */
    protected $class;
    
    
    /**
     * @var DropdownSelectOption
     */
    protected $presentation;
    
    
    /**
     * @var TextBox
     */
    protected $maxProducts;
    
    
    /**
     * @var CheckboxOption
     */
    protected $random;
    
    
    /**
     * @var DropdownSelectOption
     */
    protected $itemsPerRowXs;
    
    
    /**
     * @var DropdownSelectOption
     */
    protected $itemsPerRowSm;
    
    
    /**
     * @var DropdownSelectOption
     */
    protected $itemsPerRowMd;
    
    
    /**
     * @var DropdownSelectOption
     */
    protected $itemsPerRowLg;
    
    
    /**
     * @var ProductListGroupOption
     */
    protected $productlist;
    
    
    /**
     * @var ProductReadService
     */
    protected $productReadService;
    
    
    /**
     * Create ProductListWidget instance
     *
     * @param string     $static_id  Static ID
     * @param FieldSet[] $fieldsets  Fieldsets
     * @param stdClass   $jsonObject JSON
     *
     * @throws Exception
     */
    public function __construct(string $static_id, array $fieldsets, stdClass $jsonObject)
    {
        parent::__construct($static_id, $fieldsets, $jsonObject);
        $this->productReadService = StaticGXCoreLoader::getService('ProductRead');
    }
    
    
    /**
     * Output the HTML representation
     *
     * @param Language|null $currentLanguage
     *
     * @return string
     * @throws Exception
     */
    public function previewContent(?Language $currentLanguage): string
    {
        $itemsPerRow = $this->getSliderItemsPerRow();
        $maxProducts = min($this->maxProducts->value($currentLanguage),
                           ProductListWidgetCommandConfiguration::MAX_PRODUCTS_LIMIT);
        
        $params = [
            'categoryId'    => $this->productlist->categorySearchBox()->value()->id ?? 0,
            'listType'      => $this->productlist->listType()->value($currentLanguage),
            'random'        => $this->random->value($currentLanguage),
            'itemsPerRowXs' => $itemsPerRow['xs'],
            'itemsPerRowSm' => $itemsPerRow['sm'],
            'itemsPerRowMd' => $itemsPerRow['md'],
            'itemsPerRowLg' => $itemsPerRow['lg'],
            'maxProducts'   => (int)$maxProducts,
            'id'            => $this->id->value($currentLanguage),
            'class'         => $this->class->value($currentLanguage),
            'languageId'    => $currentLanguage?->id(),
            'languageCode'  => $currentLanguage?->code(),
            'presentation'  => $this->presentation->value($currentLanguage),
        ];
        
        $configurationFactory = MainFactory::create(ProductListWidgetConfigurationFactory::class);
        
        $construct = [
            $configurationFactory->createCommandConfigurationFromArray($params),
            $this->productlist->productSearchBox()->value($currentLanguage),
        ];
        
        $result = MainFactory::create(ProductListWidgetOutputCommand::class, ...$construct)->execute() ?? '';
        
        if (!$result) {
            $result = '<div>' . '<h4 style="color: gray;">' . MainFactory::create(LanguageTextManager::class)
                    ->get_text('preview.placeholder', 'productlistWidget') . '</h4>' . '</div>';
        }
        
        $id    = $params['id'];
        $style = $params['presentation'] === 'list' ? $this->getSliderPerViewCss($params['id'], 'gambio') : '';
        
        return $style . "<div id='{$id}-wrapper'>" . $result . "</div>";
    }
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        $categoryId = $this->productlist->categorySearchBox()->value()->id ?? 0;
        $listType   = $this->productlist->listType()->value($currentLanguage);
        $random     = $this->random->value($currentLanguage) ? 'true' : 'false';
        
        $itemsPerRow   = $this->getSliderItemsPerRow();
        $itemsPerRowXs = $itemsPerRow['xs'];
        $itemsPerRowSm = $itemsPerRow['sm'];
        $itemsPerRowMd = $itemsPerRow['md'];
        $itemsPerRowLg = $itemsPerRow['lg'];
        
        $maxProducts  = (int)min($this->maxProducts->value($currentLanguage),
                                 ProductListWidgetCommandConfiguration::MAX_PRODUCTS_LIMIT);
        $products     = implode(',', $this->productlist->productSearchBox()->value($currentLanguage));
        $id           = $this->id->value($currentLanguage);
        $class        = $this->class->value($currentLanguage);
        $languageId   = $currentLanguage?->id();
        $languageCode = $currentLanguage?->code();
        $presentation = $this->presentation->value($currentLanguage);
        
        $style = $presentation === 'list' ? $this->getSliderPerViewCss($id) : '';
        
        $result = "\n{product_list categoryId='{$categoryId}' listType='{$listType}' "
                  . "presentation='{$presentation}' random='{$random}' "
                  . "itemsPerRowXs='{$itemsPerRowXs}' itemsPerRowSm='{$itemsPerRowSm}' "
                  . "itemsPerRowMd='{$itemsPerRowMd}' itemsPerRowLg='{$itemsPerRowLg}' "
                  . "maxProducts='{$maxProducts}' products='{$products}' "
                  . "id='{$id}' class='{$class}' languageId='{$languageId}' languageCode='{$languageCode}'}\n";
        
        return $style . "<div id=\"{$id}-wrapper\">" . $result . "</div>";
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result            = $this->jsonObject;
        $result->id        = $this->static_id;
        $result->type      = 'productlist';
        $result->fieldsets = $this->fieldsets;
        
        return $result;
    }
    
    
    /**
     * @return array
     */
    protected function getSliderItemsPerRow(): array
    {
        return [
            'xs' => $this->itemsPerRowXs ? $this->itemsPerRowXs->value() : 2,
            'sm' => $this->itemsPerRowSm ? $this->itemsPerRowSm->value() : 3,
            'md' => $this->itemsPerRowMd ? $this->itemsPerRowMd->value() : 2,
            'lg' => $this->itemsPerRowLg ? $this->itemsPerRowLg->value() : 5,
        ];
    }
    
    
    /**
     * @param string $id
     * @param string $tagType
     *
     * @return string
     */
    protected function getSliderPerViewCss(string $id, string $tagType = 'style'): string
    {
        $itemsPerRow = $this->getSliderItemsPerRow();
        
        $style = $tagType === 'style' ? '<style type="text/css">' : '<gambio-style>';
        $style .= "#{$id} .productlist { display: grid; }";
        $style .= "#{$id} .productlist .product-container { width: 100% }";
        $style .= "#{$id} .productlist:before, #{$id} .productlist:after { display: none; }";
        $style .= "@media (max-width: 767px) {";
        $style .= "#{$id} .productlist { grid-template-columns: repeat({$itemsPerRow['xs']}, 1fr); }";
        $style .= "}";
        $style .= "@media (min-width: 768px) {";
        $style .= "#{$id} .productlist { grid-template-columns: repeat({$itemsPerRow['sm']}, 1fr); }";
        $style .= "}";
        $style .= "@media (min-width: 992px) {";
        $style .= "#{$id} .productlist { grid-template-columns: repeat({$itemsPerRow['md']}, 1fr); }";
        $style .= "}";
        $style .= "@media (min-width: 1200px) {";
        $style .= "#{$id} .productlist { grid-template-columns: repeat({$itemsPerRow['lg']}, 1fr); }";
        $style .= "}";
        $style .= $tagType === 'style' ? '</style>' : '</gambio-style>';
        
        return $style;
    }
}