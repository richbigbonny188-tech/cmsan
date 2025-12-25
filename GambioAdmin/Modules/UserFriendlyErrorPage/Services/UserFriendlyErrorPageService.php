<?php
/* --------------------------------------------------------------
   UserFriendlyErrorPageService.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage\Services;

/**
 * Interface UserFriendlyErrorPageService
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\Services
 */
interface UserFriendlyErrorPageService
{
    public const PAGE_NOT_FOUND_TYPE   = 'code_404';
    public const UNEXPECTED_ERROR_TYPE = 'code_500';
    
    
    /**
     * @param string $type
     *
     * @return bool
     */
    public function getUserFriendlyErrorPageActiveState(string $type): bool;
    
    
    /**
     * @param string $type
     * @param string $languageCode
     *
     * @return string
     */
    public function getUserFriendlyErrorPageFilePath(string $type, string $languageCode): string;
    
    
    /**
     * @param string $type
     * @param bool   $state
     */
    public function setUserFriendlyErrorPageActiveState(string $type, bool $state): void;
    
    
    /**
     * @param string $type
     * @param string $languageCode
     * @param string $html
     */
    public function storeUserFriendlyErrorPage(string $type, string $languageCode, string $html): void;
    
    
    /**
     * @param string $type
     */
    public function deleteUserFriendlyErrorPages(string $type): void;
}