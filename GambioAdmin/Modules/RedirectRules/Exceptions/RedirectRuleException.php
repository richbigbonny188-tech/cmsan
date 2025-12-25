<?php
/* --------------------------------------------------------------
   RedirectRuleException.php 2020-07-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules\Exceptions;

use Exception;

class RedirectRuleException extends Exception
{
    /** @var string */
    protected $userMessage = '';
    
    
    /**
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }
    
    
    /**
     * @param string $userMessage
     */
    public function setUserMessage(string $userMessage): void
    {
        $this->userMessage = $userMessage;
    }
    
}
