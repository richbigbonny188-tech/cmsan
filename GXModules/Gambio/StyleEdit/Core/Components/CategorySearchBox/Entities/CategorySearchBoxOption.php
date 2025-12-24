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

namespace Gambio\StyleEdit\Core\Components\CategorySearchBox\Entities;

use Gambio\StyleEdit\Configurations\StyleEditCategorySearchByTermEndpoint;
use Gambio\StyleEdit\Core\Components\SearchBox\Entities\SearchBoxOption;

/**
 * Class CategorySearchBoxOption
 * @package Gambio\StyleEdit\Core\Components\ProductSearchBox\Entities
 */
class CategorySearchBoxOption extends SearchBoxOption
{
    
    /**
     * CategorySearchBoxOption constructor.
     *
     * @param StyleEditCategorySearchByTermEndpoint $endpoint
     */
    public function __construct(StyleEditCategorySearchByTermEndpoint $endpoint)
    {
        parent::__construct($endpoint->value());
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'categorysearchbox';
    }
}
