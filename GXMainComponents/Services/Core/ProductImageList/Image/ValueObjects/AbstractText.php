<?php
/* --------------------------------------------------------------
   AbstractText.php 2022-08-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\ProductImageList\Image\ValueObjects;

use JsonSerializable;

/**
 * Class AbstractText
 * @package Gambio\ProductImageList\Image\ValueObjects
 */
abstract class AbstractText implements JsonSerializable
{
    const TEXT_TYPE_TITLE       = "title";
    const TEXT_TYPE_ALT_TITLE   = "alt_title";
    
    /**
     * @var Id
     */
    protected $imageId;
    
    /**
     * @var string
     */
    protected $type;
    
    /**
     * @var string
     */
    protected $value;
    
    /**
     * @var LanguageCode
     */
    protected $languageCode;
    
    
    /**
     * AbstractText constructor.
     *
     * @param Id           $imageId
     * @param string       $value
     * @param LanguageCode $languageCode
     */
    public function __construct(
        Id $imageId,
        string $value,
        LanguageCode $languageCode
    ) {
        
        $this->imageId      = $imageId;
        $this->value        = $value;
        $this->languageCode = $languageCode;
    }
    
    /**
     * @return Id
     */
    public function imageId() : Id
    {
        return $this->imageId;
    }
    
    
    /**
     * @return string
     */
    abstract public function type() : string;
    
    
    /**
     * @return string
     */
    public function value() : string
    {
        return $this->value;
    }
    
    
    /**
     * @return LanguageCode
     */
    public function languageCode() : LanguageCode
    {
        return $this->languageCode;
    }
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (object)[
            'value'        => $this->value(),
            'languageCode' => $this->languageCode()
        ];
    }
}