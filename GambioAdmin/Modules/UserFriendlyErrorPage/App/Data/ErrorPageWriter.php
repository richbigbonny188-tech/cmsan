<?php
/* --------------------------------------------------------------
   ErrorPageWriter.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data;

/**
 * Interface ErrorPageWriter
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data
 */
interface ErrorPageWriter
{
    /**
     * @return string
     */
    public function getType(): string;
    
    
    /**
     * @param bool $state
     */
    public function setUserFriendlyErrorPageActiveState(bool $state): void;
    
    
    /**
     * @param string $languageCode
     * @param string $html
     */
    public function storeUserFriendlyErrorPage(string $languageCode, string $html): void;
    
    
    public function deleteUserFriendlyErrorPages(): void;
}