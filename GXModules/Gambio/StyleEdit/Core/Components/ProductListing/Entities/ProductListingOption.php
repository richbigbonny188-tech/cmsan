<?php
/*--------------------------------------------------------------------------------------------------
    ProductListingOption.php 2019-09-03
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\ProductListing\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;

/**
 * Class ProductListingOption
 *
 * @package Gambio\StyleEdit\Core\Components\ProductListing\Entities
 */
class ProductListingOption extends AbstractComponentOption
{
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
     * @return string
     */
    public function type(): ?string
    {
        return 'productlisting';
    }
}
