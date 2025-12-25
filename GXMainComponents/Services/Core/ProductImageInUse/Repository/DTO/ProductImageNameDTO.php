<?php
/*--------------------------------------------------------------
   ProductImageBaseNameDTO.php 2020-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class ProductImageBaseNameDTO
 */
class ProductImageNameDTO
{
    /**
     * @var string
     */
    protected $name;
    
    
    /**
     * ProductImageBaseNameDTO constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->name;
    }
}