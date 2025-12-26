<?php
/* --------------------------------------------------------------
   RedirectRulesCollection.php 2020-07-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules\Entities;

use EditableCollection;

require_once __DIR__ . '/../../../../GXEngine/Shared/AbstractCollection.inc.php';
require_once __DIR__ . '/../../../../GXEngine/Shared/EditableCollection.php';

class RedirectRuleCollection extends EditableCollection implements RedirectRuleCollectionInterface
{
    public function _getValidType()
    {
        return RedirectRule::class;
    }
    
    
    public function addRedirectRule(RedirectRule $rule): void
    {
        $this->addItem($rule);
    }
    
    
    public function toArray(): array
    {
        return $this->getArray();
    }
}
