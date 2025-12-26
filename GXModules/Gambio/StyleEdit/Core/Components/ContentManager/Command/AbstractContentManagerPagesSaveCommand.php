<?php
/*--------------------------------------------------------------------------------------------------
    AbstractContentManagerPagesSaveCommand.php 2021-07-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Command;

use ContentDeleterInterface;
use ContentIdentificationInterface;
use ContentNotFoundException;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Interfaces\ContentManagerPagesServiceInterface;

/**
 * Class ContentManagerPagesSaveCommand
 *
 * @package Gambio\StyleEdit\Core\Components\ContentManager\Command
 */
abstract class AbstractContentManagerPagesSaveCommand extends ContentManagerSaveCommand
{
    
    /**
     * @var bool
     */
    private $isDeleted = false;
    
    
    /**
     * @var ContentDeleterInterface
     */
    protected $contentDeleteService;
    
    
    /**
     * @var ContentManagerPagesServiceInterface
     */
    protected $contentManagerPagesService;
    
    
    /**
     * WysiwygSaveCommand constructor.
     *
     * @param ContentManagerPagesServiceInterface $contentManagerPagesService
     * @param LanguageService                     $languageService
     * @param CurrentThemeInterface               $currentTheme
     */
    public function __construct(
        ContentManagerPagesServiceInterface $contentManagerPagesService,
        LanguageService $languageService,
        CurrentThemeInterface $currentTheme
    ) {
        parent::__construct($contentManagerPagesService->writeService(),
                            $contentManagerPagesService->readService(),
                            $contentManagerPagesService->contentValueObjectFactory(),
                            $languageService,
                            $currentTheme);
        
        $this->contentManagerPagesService = $contentManagerPagesService;
    }
    
    
    /**
     * @param $contentManagerContent
     */
    abstract protected function update($contentManagerContent): void;
    
    
    /**
     * @param $contentManagerContent
     */
    abstract protected function create($contentManagerContent): void;
    
    
    /**
     * @return mixed
     */
    abstract protected function createContent();
    
    
    /**
     * @param ContentIdentificationInterface $identification
     */
    private function delete(ContentIdentificationInterface $identification): void
    {
        $this->contentManagerPagesService->deletePage($identification);
    }
    
    
    /**
     *
     */
    public function execute(): void
    {
        $infoPageContent = $this->createContent();
        
        try {
            // Fetch the content manager data
            $contentManager = $this->contentManagerPagesService->findPageById($this->option->contentIdentification());
            
            // Needs to be deleted? Delete only if the entry allows it
            if ($this->isDeleted()) {
                if ($contentManager->isDeletable()) {
                    $this->delete($infoPageContent->id());
                }
            } else {
                $this->update($infoPageContent);
            }
        } catch (ContentNotFoundException $notFoundException) {
            // If the content was not found we can only create a new content if it was not set to delete
            if (!$this->isDeleted()) {
                $this->create($infoPageContent);
            }
        }
    }
    
    
    /**
     * Sets the content manager to delete
     */
    public function setToDelete(): void
    {
        $this->isDeleted = true;
    }
    
    
    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }
}
