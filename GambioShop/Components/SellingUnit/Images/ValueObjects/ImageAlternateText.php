<?php
/*--------------------------------------------------------------------------------------------------
    ImageAlternateText.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\SellingUnit\Images\ValueObjects;

/**
 * Class ImageAlternateText
 * @package Gambio\Shop\SellingUnit\Images\ValueObjects
 */
class ImageAlternateText
{
    /**
     * @var string
     */
    private $value;
    
    
    /**
     * ImageAlternateText constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
}