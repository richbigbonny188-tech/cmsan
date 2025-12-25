<?php
/**
 * ShortDescriptionDto.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\Representation\ShortDescription\Repository\DTO;

/**
 * Class ShortDescriptionDto
 * @package Gambio\Shop\Product\Representation\ShortDescription\Repository\DTO
 */
class ShortDescriptionDto
{
    /**
     * @var string
     */
    protected $shortDescription;
    
    
    public function __construct(string $shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }
    
    
    /**
     * @return string
     */
    public function shortDescription(): string
    {
        return $this->shortDescription;
    }
}