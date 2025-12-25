<?php
/*--------------------------------------------------------------------------------------------------
    ProductWidget.php 2019-10-24
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use Gambio\StyleEdit\Core\Components\ProductSearchBox\Entities\ProductSearchBoxOption;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Options\Entities\FieldSet;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\ContentGeneratorInterface;

/**
 * Class ButtonWidget
 */
class ProductWidget extends AbstractWidget
{
    
    /**
     * @var TextBox
     */
    protected $class;
    
    /**
     * @var ProductSearchBoxOption
     */
    protected $productSearchBox;
    
    /**
     * @var string
     */
    protected const PRODUCT_FALLBACK = 1;
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    public function htmlContent(?Language $currentLanguage) : string
    {
        if ($this->productSearchBox->value()->id) {
            $productId = $this->productSearchBox->value()->id;
            $elementId = $this->id->value();
            $class     = $this->class->value();
            $languageId = $currentLanguage->id();
            $languageCode = $currentLanguage->code();

            return "\n{product id=$productId elementId='$elementId' class='$class' languageId='$languageId' languageCode='$languageCode'}\n";
        }
        
        return '';
    }


    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     * @throws Exception
     */
    public function previewContent(?Language $currentLanguage) : string
    {
        if ($this->productSearchBox->value()->id) {
            $params = [
                'id'           => $this->id->value($currentLanguage),
                'class'        => $this->class->value($currentLanguage),
                'languageId'   => $currentLanguage->id(),
                'languageCode' => $currentLanguage->code()
            ];

            $configurationFactory = MainFactory::create(ProductWidgetConfigurationFactory::class);
            $commandConfig = $configurationFactory->createCommandConfigurationFromArray($params);
            return MainFactory::create(
                    ProductWidgetOutputCommand::class,
                    new IdType($this->productSearchBox->value()->id),
                    $commandConfig
                )->execute() ?? '';
        } else {
            return '<div>' . '<h4 style="color: gray;">' . MainFactory::create(LanguageTextManager::class)->get_text(
                    'preview.placeholder',
                    'productWidget'
                ) . '</h4>' . '</div>';
        }
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize() : stdClass
    {
        $result            = $this->jsonObject;
        $result->id        = $this->static_id;
        $result->type      = 'product';
        $result->fieldsets = $this->fieldsets;
        
        return $result;
    }
}
