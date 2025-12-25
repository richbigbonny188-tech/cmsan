<?php
/* --------------------------------------------------------------
   PageNotFoundErrorPageReader.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Reader;

use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\ErrorPageReader;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class PageNotFoundErrorPageReader
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Reader
 */
class PageNotFoundErrorPageReader implements ErrorPageReader
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    /**
     * @var string
     */
    private $errorPagesDir;
    
    
    /**
     * PageNotFoundErrorPageReader constructor.
     *
     * @param ConfigurationService $configurationService
     * @param string               $errorPagesDir
     */
    public function __construct(ConfigurationService $configurationService, string $errorPagesDir)
    {
        $this->configurationService = $configurationService;
        $this->errorPagesDir        = $errorPagesDir;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'code_404';
    }
    
    
    /**
     * @inheritDoc
     */
    public function getUserFriendlyErrorPageActiveState(): bool
    {
        $activeState = $this->configurationService->find('error_pages/customPageNotFound');
        
        return $activeState !== null ? $activeState->value() === 'true' : false;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getUserFriendlyErrorPageFilePath(string $languageCode): string
    {
        return $this->errorPagesDir . '/404-' . strtolower($languageCode) . '.html';
    }
}