<?php
/*--------------------------------------------------------------------------------------------------
    CategorySearcBoxOption.php 2019-08-29
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\ProductSearchBox\Entities;

use Gambio\StyleEdit\Configurations\StyleEditProductSearchByTermEndpoint;
use Gambio\StyleEdit\Core\Components\SearchBox\Entities\SearchBoxOption;


/**
 * Class ProductSearchBoxOption
 * @package Gambio\StyleEdit\Core\Components\ProductSearchBox\Entities
 */
class ProductSearchBoxOption extends SearchBoxOption
{
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'productsearchbox';
    }
    
    
    /**
     * ProductSearchBoxOption constructor.
     *
     * @param StyleEditProductSearchByTermEndpoint $endpoint
     */
    public function __construct(StyleEditProductSearchByTermEndpoint $endpoint)
    {
        parent::__construct($endpoint->value());
    }
}
