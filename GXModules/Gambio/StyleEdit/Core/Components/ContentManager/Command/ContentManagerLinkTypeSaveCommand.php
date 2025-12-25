<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerLinkTypeSaveCommand.php 2021-05-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Command;

use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Services\ContentManagerLinkTypeService;
use LinkPageContentBuilder;

class ContentManagerLinkTypeSaveCommand extends AbstractContentManagerPagesSaveCommand
{
    
    /**
     * @var ContentManagerLinkTypeService
     */
    private $linkTypeService;
    
    
    public function __construct(
        ContentManagerLinkTypeService $linkTypeService,
        LanguageService $languageService,
        CurrentThemeInterface $currentTheme
    ) {
        parent::__construct($linkTypeService, $languageService, $currentTheme);
        $this->linkTypeService = $linkTypeService;
    }
    
    
    /**
     * @inheritDoc
     */
    protected function update($contentManagerContent): void
    {
        $this->linkTypeService->updatePage($contentManagerContent);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function create($contentManagerContent): void
    {
        $this->linkTypeService->createPage($contentManagerContent);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function createContent(): \LinkPageContent
    {
        $dbContent = [];
        
        /** @var Language $language */
        foreach ($this->languageService->getActiveLanguages() as $language) {
            
            $dbContent[] = [
                'language_id'      => $language->id(),
                'languages_id'     => $language->id(),
                'content_name'     => $this->option->value($language)['contentName'],
                'content_title'    => $this->option->value($language)['contentTitle'],
                'content_status'   => $this->option->value($language)['contentStatus'],
                'sort_order'       => $this->option->sortOrder(),
                'content_delete'   => $this->option->deletable() ? '1' : '0',
                'content_position' => $this->option->value($language)['contentPosition'],
                'gm_link'          => $this->option->value($language)['contentLink'],
                'gm_link_target'   => $this->option->value($language)['opensInNewTabStatus'],
            ];
        }
        
        $names           = $this->contentValueObjectFactory->createContentNameCollection($dbContent);
        $titles          = $this->contentValueObjectFactory->createContentTitleCollection($dbContent);
        $contentStatuses = $this->contentValueObjectFactory->createContentStatusCollection($dbContent);
        $sortOrders      = $this->contentValueObjectFactory->createContentSortOrder($dbContent);
        $deletable       = $this->contentValueObjectFactory->createContentDelete($dbContent);
        $links           = $this->contentValueObjectFactory->createContentLinkCollection($dbContent);
        $newTabStatus    = $this->contentValueObjectFactory->createContentOpenInNewTabStatusCollection($dbContent);
        
        return LinkPageContentBuilder::create()
            ->usingId($this->option->contentIdentification())
            ->inPosition($this->linkTypeService->createPagePositionFromString(current($dbContent)['content_position']))
            ->usingNames($names)
            ->usingTitles($titles)
            ->usingLinks($links)
            ->usingOpenInNewTabStatus($newTabStatus)
            ->usingStatus($contentStatuses)
            ->usingSortOrder($sortOrders)
            ->usingDelete($deletable)
            ->build();
    }
}
