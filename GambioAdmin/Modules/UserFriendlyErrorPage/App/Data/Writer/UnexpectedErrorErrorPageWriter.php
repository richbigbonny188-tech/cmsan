<?php
/* --------------------------------------------------------------
   UnexpectedErrorErrorPageWriter.php 2021-01-08
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
use Gambio\Core\Logging\LoggerBuilder;
use Psr\Log\LoggerInterface;

/**
 * Class UnexpectedErrorErrorPageWriter
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Writer
 */
class UnexpectedErrorErrorPageWriter implements ErrorPageWriter
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
     * @var string
     */
    private $errorPagesDir;
    
    /**
     * @var string
     */
    private $pathToFlagFile;
    
    
    /**
     * UnexpectedErrorErrorPageWriter constructor.
     *
     * @param LoggerBuilder      $loggerBuilder
     * @param ErrorPageGenerator $generator
     * @param string             $errorPagesDir
     * @param string             $pathToFlagFile
     */
    public function __construct(
        LoggerBuilder $loggerBuilder,
        ErrorPageGenerator $generator,
        string $errorPagesDir,
        string $pathToFlagFile
    ) {
        $this->logger         = $loggerBuilder->omitRequestData()->build();
        $this->generator      = $generator;
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
    public function setUserFriendlyErrorPageActiveState(bool $state): void
    {
        if ($state) {
            if (!file_exists($this->pathToFlagFile) && @touch($this->pathToFlagFile) === false) {
                $this->logger->error('Could not create user friendly error page state file.',
                                     ['type' => '500', 'file' => $this->pathToFlagFile]);
            }
            
            return;
        }
        
        if (file_exists($this->pathToFlagFile) && @unlink($this->pathToFlagFile) === false) {
            $this->logger->error('Could not delete user friendly error page state file.',
                                 ['type' => '500', 'file' => $this->pathToFlagFile]);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeUserFriendlyErrorPage(string $languageCode, string $html): void
    {
        $file = $this->errorPagesDir . '/500-' . strtolower($languageCode) . '.html';
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
        foreach (glob($this->errorPagesDir . '/500-*.html') as $filePath) {
            if (@unlink($filePath) === false) {
                $this->logger->error('Could not delete user friendly error page.', ['file' => $filePath]);
            }
        }
    }
}