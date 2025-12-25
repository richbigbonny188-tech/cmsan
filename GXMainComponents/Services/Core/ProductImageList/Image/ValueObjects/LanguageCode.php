<?php
/* --------------------------------------------------------------
  LanguageCode.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\ValueObjects;

use JsonSerializable;

/**
 * Class LanguageCode
 * @package Gambio\ProductImageList\Image\ValueObjects
 */
class LanguageCode implements JsonSerializable
{
    /**
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * LanguageCode constructor.
     *
     * @param string $languageCode
     */
    public function __construct(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value();
    }
}