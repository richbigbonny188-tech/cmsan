<?php
/**
 * ShortDescription.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\ValueObjects;

/**
 * Class ShortDescription
 * @package Gambio\Shop\SellingUnit\Presentation\ValueObjects
 */
class ShortDescription
{
    /**
     * @var string
     */
    protected $shortDescription;
    
    
    /**
     * ShortDescription constructor.
     *
     * @param string $shortDescription
     */
    public function __construct(string $shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->shortDescription;
    }
}