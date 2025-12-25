<?php
/*--------------------------------------------------------------------------------------------------
    PagesGroupOption.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\PageGroup\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentGroupOption;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Components\Page\Entities\PageOption;

class PageGroupOption extends AbstractComponentGroupOption
{
    /**
     * @var PageOption[]
     */
    protected $items = [];
    
    /**
     * @var PageOption
     */
    protected $page;
    
    /**
     * @var string
     */
    protected $title;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->page = SingletonPrototype::instance()->get('PageOption');
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
    
    
    /**
     * @param $object
     *
     * @throws \Exception
     */
    public function initializeFromJsonObject($object): void
    {
        if (isset($object->id)) {
            $this->id = $object->id;
        }
        
        if (isset($object->title)) {
            $this->title = $object->title;
        }
        
        if (isset($object->items)) {
            foreach ($object->items as $item) {
                $this->items[] = PageOption::createFromJsonObject($item);
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    protected function isValid($value): bool
    {
        return true;
    }
    
    
    /**
     * @inheritDoc
     */
    protected function parseValue($value)
    {
        return $value;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getGroupOptions()
    {
        return [
            $this->page()
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): ?string
    {
        return 'page-group';
    }
    
    
    public function page(): PageOption
    {
        return $this->page;
    }
    
    
    /**
     * @return PageOption[]
     */
    public function items(): array
    {
        return $this->items;
    }
    
    
    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
    
    
    /**
     * @return object
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = [
            'id'    => $this->id(),
            'title' => $this->title(),
            'type'  => $this->type(),
            'items' => []
        ];
    
        foreach ($this->items() as $item) {
            $result['items'][] = $item->jsonSerialize();
        }
    
        return (object)$result;
    }
}
