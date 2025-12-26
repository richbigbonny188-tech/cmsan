<?php
/* --------------------------------------------------------------
   UserFriendlyErrorPageErrorHandlerService.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage\App;

use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Reader\UnexpectedErrorErrorPageReader;
use Gambio\Admin\Modules\UserFriendlyErrorPage\Services\UserFriendlyErrorPageErrorHandlerService as UserFriendlyErrorPagesErrorHandlerServiceInterface;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class UserFriendlyErrorPageErrorHandlerService
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App
 * @codeCoverageIgnore
 */
class UserFriendlyErrorPageErrorHandlerService implements UserFriendlyErrorPagesErrorHandlerServiceInterface
{
    
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var UnexpectedErrorErrorPageReader
     */
    private $reader;
    
    
    /**
     * UserFriendlyErrorPageErrorHandlerService constructor.
     *
     * @param Url                            $url
     * @param UnexpectedErrorErrorPageReader $reader
     */
    public function __construct(Url $url, UnexpectedErrorErrorPageReader $reader)
    {
        $this->url    = $url;
        $this->reader = $reader;
    }
    
    
    /**
     * @inheritDoc
     */
    public function redirectToUserFriendlyErrorPage(): string
    {
        $errorCode   = $this->generateErrorCode();
        $redirectUrl = $this->url->base() . '/error.php?code=' . urlencode($errorCode);
        
        header("HTTP/1.1 307	Temporary Redirect");
        header('Cache-Control: no-cache');
        header('Location: ' . $redirectUrl);
        
        return $errorCode;
    }
    
    
    /**
     * @inheritDoc
     */
    public function isModuleActive(): bool
    {
        return $this->reader->getUserFriendlyErrorPageActiveState();
    }
    
    
    /**
     * @return string
     */
    private function generateErrorCode(): string
    {
        return strtoupper(uniqid('error-', true));
    }
}