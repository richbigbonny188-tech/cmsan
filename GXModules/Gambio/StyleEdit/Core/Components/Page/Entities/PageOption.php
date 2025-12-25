<?php
/*--------------------------------------------------------------------------------------------------
    PagesOption.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\Page\Entities;

use Gambio\StyleEdit\Core\Components\ContentManager\Entities\AbstractContentManagerOption;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;
use Gambio\StyleEdit\Core\SingletonPrototype;

class PageOption extends AbstractContentManagerOption
{
    /**
     * @var string
     */
    protected $pageType;
    
    /**
     * @var bool
     */
    protected $deletable;
    
    /**
     * @var int
     */
    protected $sortOrder;
    
    /**
     * @var string
     */
    protected $publicLink;
    
    /**
     * @var bool
     */
    protected $customerStatusCheck;
    
    /**
     * @var string
     */
    protected $alias;
    
    
    public function initializeFromJsonObject($object): void
    {
        
        if (isset($object->content)) {
            foreach ($object->content as $langCode => $content) {
                foreach ($content as $contentElementName => $contentElementValue) {
                    $object->value[$langCode][$contentElementName] = $contentElementValue;
                }
                
                // Fills the contentName with the content title if the contentName is empty
                if (empty($object->value[$langCode]['contentName'])
                    && !empty($object->value[$langCode]['contentTitle'])) {
                    $object->value[$langCode]['contentName'] = $object->value[$langCode]['contentTitle'];
                }
            }
            
            $object->value = (object)$object->value;
        }
        
        parent::initializeFromJsonObject($object);
        
        $this->translatable        = true;
        $this->deletable           = $object->deletable;
        $this->publicLink          = $object->publicLink;
        $this->pageType            = $object->pageType;
        $this->id                  = $object->id;
        $this->sortOrder           = $object->sortOrder;
        $this->customerStatusCheck = $object->customerStatusCheck;
        $this->type                = $this->type();
    }
    
    
    public static function createFromJsonObject(
        $jsonObject,
        $prefix = '',
        SettingsRepository $configurationRepository = null
    ) {
        $result = SingletonPrototype::instance()->get(static::class);
        $result->initializeFromJsonObject($jsonObject);
        
        return $result;
    }
    
    
    protected function isValid($value): bool
    {
        return true;
    }
    
    
    protected function parseValue($value)
    {
        return $value;
    }
    
    
    public function type(): ?string
    {
        return 'page';
    }
    
    
    /**
     * @return string
     */
    public function pageType(): string
    {
        return $this->pageType;
    }
    
    
    /**
     * @return bool
     */
    public function deletable(): bool
    {
        return $this->deletable;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * @return string
     */
    public function publicLink(): string
    {
        return $this->publicLink;
    }
    
    
    /**
     * @return bool
     */
    public function customerStatusCheck(): bool
    {
        return $this->customerStatusCheck;
    }
    
    
    #[\ReturnTypeWillChange]
    
    
    public function jsonSerialize()
    {
        return [
            'id'                  => $this->id(),
            'type'                => $this->type(),
            'contentAlias'        => $this->contentIdentification()->contentAlias(),
            'contentGroup'        => $this->contentIdentification()->contentGroup(),
            'sortOrder'           => $this->sortOrder(),
            'deletable'           => $this->deletable(),
            'pageType'            => $this->pageType(),
            'publicLink'          => $this->publicLink(),
            'customerStatusCheck' => $this->customerStatusCheck(),
            'content'             => $this->value,
        
        ];
    }
}
