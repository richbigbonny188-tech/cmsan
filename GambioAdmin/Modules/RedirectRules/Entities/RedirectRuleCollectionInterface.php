<?php
/* --------------------------------------------------------------
   RedirectRulesCollectionInterface.php 2020-07-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules\Entities;

interface RedirectRuleCollectionInterface
{
    public function addRedirectRule(RedirectRule $rule): void;
    
    
    public function toArray(): array;
}
