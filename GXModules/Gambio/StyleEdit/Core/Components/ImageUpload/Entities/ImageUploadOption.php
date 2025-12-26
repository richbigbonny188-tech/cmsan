<?php
/* --------------------------------------------------------------
   ImageUploadOption.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Components\ImageUpload\Entities;

use Exception;
use Gambio\StyleEdit\Configurations\ShopBaseUrl;
use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use Gambio\StyleEdit\Core\SingletonPrototype;
use stdClass;

/**
 * Class ImageUploadOption
 * @package Gambio\StyleEdit\Core\Components\ImageUpload\Entities
 */
class ImageUploadOption extends AbstractComponentOption
{
    protected const ABSOLUTE_URL_PATTERN = '#^(http|https)://#';
    
    protected $holder;
    
    
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
     * @throws Exception
     */
    protected function parseValue($value)
    {
        
        $searchValue = SingletonPrototype::instance()->get(ShopBaseUrl::class)->value();
        $baseUrlToken = '__SHOP_BASE_URL__';
        
        //  front-end sends absolute url stored is the relative
        if ($value instanceof stdClass) {
            
            foreach ($value as $languageCode => &$val) {
                if (preg_match(self::ABSOLUTE_URL_PATTERN, $val) === 1) {
                    $val = str_replace($searchValue, '', $val);
                }

                if (strpos($val, $baseUrlToken) === 0) {
                    
                    $val = str_replace($baseUrlToken, '', $val);
                }
            }
            unset($val);
        } elseif (preg_match(self::ABSOLUTE_URL_PATTERN, $value) === 1) {
            
            $value = str_replace($searchValue, '', $value);
        } elseif (strpos($value, $baseUrlToken) === 0) {
    
            $value = str_replace($baseUrlToken, '', $value);
        }
        
        return $value;
    }
    
    
    /**
     * @return mixed
     */
    protected function holder()
    {
        return $this->holder;
    }
    
    
    /**
     * @param $object
     *
     * @throws Exception
     */
    public function initializeFromJsonObject($object): void
    {
        parent::initializeFromJsonObject($object);
        
        if (isset($object->holder)) {
            $this->holder = $object->holder;
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
        $result = parent::jsonSerialize();
        
        if ($this->holder()) {
            $result->holder = $this->holder();
        }
        
        return (object)$result;
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'imageupload';
    }
}