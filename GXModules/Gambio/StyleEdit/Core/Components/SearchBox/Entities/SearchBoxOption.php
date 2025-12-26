<?php
/*--------------------------------------------------------------------------------------------------
    SearchBoxOption.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\SearchBox\Entities;

use Exception;
use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use stdClass;

/**
 * Class SearchBoxOption
 * @package Gambio\StyleEdit\Core\Components\SearchBox\Entities
 */
class SearchBoxOption extends AbstractComponentOption
{
    /**
     * @var string
     */
    protected $endpoint;
    
    
    /**
     * SearchBoxOption constructor.
     *
     * @param string $endpoint
     */
    public function __construct(string $endpoint = '')
    {
        parent::__construct();
        $this->endpoint = $endpoint;
    }
    
    
    /**
     * @return string
     */
    protected function endpoint(): string
    {
        return $this->endpoint;
    }
    
    
    /**
     * @param $value
     *
     * @return boolean
     */
    protected function isValid($value): bool
    {
        return true;
    }
    
    
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function parseValue($value)
    {
        return $value;
    }
    
    
    /**
     * @param $object
     *
     * @throws Exception
     */
    public function initializeFromJsonObject($object): void
    {
        parent::initializeFromJsonObject($object);
        
        if (isset($object->endpoint)) {
            $this->endpoint = $object->endpoint;
        }
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        /** @var stdClass $result */
        $result           = parent::jsonSerialize();
        $result->endpoint = $this->endpoint();
        
        return $result;
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'searchbox';
    }
}