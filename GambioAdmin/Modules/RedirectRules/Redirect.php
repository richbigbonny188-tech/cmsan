<?php
/* --------------------------------------------------------------
   Redirect.php 2020-06-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules;

class Redirect implements RedirectInterface
{
    /** @var string */
    protected $targetUrl;
    
    /** @var int */
    protected $redirectType;
    
    
    public function __construct(string $targetUrl, int $redirectType = 302)
    {
        $this->targetUrl    = $targetUrl;
        $this->redirectType = $redirectType;
    }
    
    
    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }
    
    
    public function getRedirectType(): int
    {
        return $this->redirectType;
    }
}
