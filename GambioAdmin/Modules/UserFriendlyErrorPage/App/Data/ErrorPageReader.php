<?php
/* --------------------------------------------------------------
   ErrorPageReader.php 2021-01-08
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
 * Interface ErrorPageReader
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data
 */
interface ErrorPageReader
{
    /**
     * @return string
     */
    public function getType(): string;
    
    
    /**
     * @return bool
     */
    public function getUserFriendlyErrorPageActiveState(): bool;
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function getUserFriendlyErrorPageFilePath(string $languageCode): string;
}