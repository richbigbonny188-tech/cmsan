<?php
/* --------------------------------------------------------------
   RedirectServiceInterface.php 2020-07-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules;

use Gambio\Admin\Modules\RedirectRules\Entities\RedirectRule;
use Gambio\Admin\Modules\RedirectRules\Entities\RedirectRuleCollectionInterface;

interface RedirectServiceInterface
{
    public function findRedirectByRelativeUri(string $relativeUri): ?RedirectInterface;
    
    
    public function getRedirectRules(): RedirectRuleCollectionInterface;
    
    
    public function makeRedirectRule(
        int $id,
        string $urlPath,
        string $query,
        string $queryMatchMode,
        int $responseCode,
        string $target,
        string $queryProcessing,
        bool $status
    ): RedirectRule;
    
    
    public function addRedirectRule(RedirectRule $redirectRule): RedirectRule;
    
    
    public function deleteRedirectRule(int $ruleId): void;
    
    
    public function enableRedirectRule(int $ruleId): void;
    
    
    public function disableRedirectRule(int $ruleId): void;
    
    
    public function updateRedirectRule(RedirectRule $redirectRule): void;
}
