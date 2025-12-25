<?php
/* --------------------------------------------------------------
   RedirectRepositoryInterface.php 2020-07-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules\Repository;

use Gambio\Admin\Modules\RedirectRules\Entities\RedirectRule;

interface RedirectRepositoryInterface
{
    public function getRedirectRule(int $ruleId): RedirectRule;
    
    
    public function findRedirectRulesByPath(string $path): array;
    
    
    public function findRedirectRulesByQuery(string $query): array;
    
    
    public function getNumberOfPages(int $pageSize): int;
    
    
    public function getAllRedirectRules(int $pageNumber, int $pageSize): array;
    
    
    public function addRedirectRule(RedirectRule $redirectRule): RedirectRule;
    
    
    public function deleteRedirectRule(int $ruleId): void;
    
    
    public function updateRedirectRule(RedirectRule $redirectRule);
}
