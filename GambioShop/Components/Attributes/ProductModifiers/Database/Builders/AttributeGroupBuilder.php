<?php
/*--------------------------------------------------------------------------------------------------
    AttributeGroupBuilder.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);

namespace Gambio\Shop\Attributes\ProductModifiers\Database\Builders;

use Gambio\Shop\Attributes\ProductModifiers\Database\AttributeGroup;
use Gambio\Shop\ProductModifiers\Groups\Builders\AbstractGroupBuilder;
use Gambio\Shop\ProductModifiers\Groups\GroupInterface;

/**
 * Class AttributeGroupBuilder
 * @package Gambio\Shop\Attributes\ProductModifiers\Database\Builders
 */
class AttributeGroupBuilder extends AbstractGroupBuilder
{
    
    /**
     * @inheritDoc
     */
    protected function createInstance(): GroupInterface
    {
        return new AttributeGroup($this->id, $this->type, $this->name, $this->status, $this->modifiers());
    }
}