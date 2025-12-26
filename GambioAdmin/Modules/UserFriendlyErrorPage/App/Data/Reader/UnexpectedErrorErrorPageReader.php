<?php
/* --------------------------------------------------------------
   UnexpectedErrorErrorPageReader.php 2021-07-12
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
use Gambio\Core\Application\ValueObjects\Environment;

/**
 * Class UnexpectedErrorErrorPageReader
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Reader
 */
class UnexpectedErrorErrorPageReader implements ErrorPageReader
{
    /**
     * @var Environment
     */
    private $environment;
    
    /**
     * @var string
     */
    private $errorPagesDir;
    
    /**
     * @var string
     */
    private $pathToFlagFile;
    
    
    /**
     * UnexpectedErrorErrorPageReader constructor.
     *
     * @param Environment $environment
     * @param string      $errorPagesDir
     * @param string      $pathToFlagFile
     */
    public function __construct(Environment $environment, string $errorPagesDir, string $pathToFlagFile)
    {
        $this->environment    = $environment;
        $this->errorPagesDir  = $errorPagesDir;
        $this->pathToFlagFile = $pathToFlagFile;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'code_500';
    }
    
    
    /**
     * @inheritDoc
     */
    public function getUserFriendlyErrorPageActiveState(): bool
    {
        return file_exists($this->pathToFlagFile);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getUserFriendlyErrorPageFilePath(string $languageCode): string
    {
        return $this->errorPagesDir . '/500-' . strtolower($languageCode) . '.html';
    }
}