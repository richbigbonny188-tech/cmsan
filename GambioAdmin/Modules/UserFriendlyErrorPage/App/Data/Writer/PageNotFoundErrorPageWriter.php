<?php
/* --------------------------------------------------------------
   PageNotFoundErrorPageWriter.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Writer;

use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\ErrorPageWriter;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Logging\LoggerBuilder;
use Psr\Log\LoggerInterface;

/**
 * Class PageNotFoundErrorPageWriter
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Writer
 */
class PageNotFoundErrorPageWriter implements ErrorPageWriter
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * @var ErrorPageGenerator
     */
    private $generator;
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    /**
     * @var string
     */
    private $errorPagesDir;
    
    
    /**
     * UnexpectedErrorErrorPageWriter constructor.
     *
     * @param LoggerBuilder        $loggerBuilder
     * @param ErrorPageGenerator   $generator
     * @param ConfigurationService $configurationService
     * @param string               $errorPagesDir
     */
    public function __construct(
        LoggerBuilder $loggerBuilder,
        ErrorPageGenerator $generator,
        ConfigurationService $configurationService,
        string $errorPagesDir
    ) {
        $this->logger               = $loggerBuilder->omitRequestData()->build();
        $this->generator            = $generator;
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
    public function setUserFriendlyErrorPageActiveState(bool $state): void
    {
        $this->configurationService->save('error_pages/customPageNotFound', $state ? 'true' : 'false');
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeUserFriendlyErrorPage(string $languageCode, string $html): void
    {
        $file = $this->errorPagesDir . '/404-' . strtolower($languageCode) . '.html';
        $html = $this->generator->generateErrorPageHtml($html);
        
        if (@file_put_contents($file, $html) === false) {
            $this->logger->error('Could not store user friendly error page.',
                                 ['file' => $file]);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteUserFriendlyErrorPages(): void
    {
        foreach (glob($this->errorPagesDir . '/404-*.html') as $filePath) {
            if (@unlink($filePath) === false) {
                $this->logger->error('Could not delete user friendly error page.', ['file' => $filePath]);
            }
        }
    }
}