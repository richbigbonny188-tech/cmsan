<?php
/*--------------------------------------------------------------------------------------------------
    ContentZoneOption.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\ContentZone\Entities;

use Exception;
use Gambio\StyleEdit\Core\Options\Entities\AbstractOption;
use Gambio\StyleEdit\Core\Components\ContentZone\Interfaces\UpdatableContentZoneContentInterface;
use JsonSerializable;

/**
 * Class ContentZoneOption
 * @package Gambio\StyleEdit\Core\Components\ContentZone\Entities
 */
class ContentZoneOption extends AbstractOption implements UpdatableContentZoneContentInterface
{
    /**
     * @var ContentZoneContent Rows collection.
     */
    protected $content;
    
    protected $originalJson;
    
    
    /**
     * @param $value
     *
     * @return mixed|void
     */
    public function parseValue($value)
    {
    }
    
    
    /**
     * @param $value
     *
     * @return bool
     */
    public function isValid($value): bool
    {
        return true;
    }
    
    
    /**
     * @return ContentZoneContent
     */
    public function content(): ContentZoneContent
    {
        return $this->content;
    }
    
    
    /**
     * @param $jsonObject
     *
     * @throws Exception
     */
    public function initializeFromJsonObject($jsonObject): void
    {
        parent::initializeFromJsonObject($jsonObject);
        $this->originalJson = $jsonObject;
        
        $this->content = ContentZoneContent::createFromJsonObject($jsonObject);
    }
    
    
    /**
     * @return mixed|void
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->content->jsonSerialize();
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'content-zone';
    }
    
    
    public function update(): void
    {
        $this->content->update();
    }
}
