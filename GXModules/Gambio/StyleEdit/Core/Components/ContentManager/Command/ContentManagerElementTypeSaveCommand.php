<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerElementSaveCommand.php 2021-05-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Command;

use ContentIdentificationInterface;
use ContentNotFoundException;
use ContentStatus;
use ElementPosition;
use Gambio\StyleEdit\Core\Components\ContentManager\Entities\AbstractContentManagerOptionValue;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Services\ContentManagerElementTypeService;
use InfoElementContentBuilder;

/**
 * Class ContentManagerElementTypeSaveCommand
 * @package Gambio\StyleEdit\Core\Components\ContentManager\Command
 */
class ContentManagerElementTypeSaveCommand extends AbstractContentManagerPagesSaveCommand
{
    /**
     * @var string
     */
    protected $defaultTitle;
    
    /**
     * @var ContentManagerElementTypeService
     */
    private $elementTypeService;
    
    
    public function __construct(
        ContentManagerElementTypeService $elementTypeService,
        LanguageService $languageService,
        CurrentThemeInterface $currentTheme
    ) {
        parent::__construct($elementTypeService, $languageService, $currentTheme);
        
        $this->elementTypeService = $elementTypeService;
    }
    
    
    /**
     * @return string
     * @throws \Exception
     */
    protected function defaultTitle(): string
    {
        if ($this->defaultTitle === null) {
            
            $hash = date('Y-m-d H:i:s') . random_int(0, 100);
            $hash = md5($hash);
            $hash = substr($hash, 0, 6);
            
            $this->defaultTitle = 'StyleEdit-' . $hash;
        }
        
        return $this->defaultTitle;
    }
    
    /**
     * @inheritDoc
     */
    protected function update($contentManagerContent): void
    {
        $this->elementTypeService->updatePage($contentManagerContent);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function create($contentManagerContent): void
    {
        $this->elementTypeService->createPage($contentManagerContent);
    }
    
    
    /**
     * @return \InfoElementContent
     * @throws \UnfinishedBuildException
     */
    protected function createContent(): \InfoElementContent
    {
        $headingsContent = $textsContent = $titlesContent = $statusContent = [];
    
        /** @var Language $language */
        foreach ($this->languageService->getActiveLanguages() as $language) {
            $title = $this->defaultTitle();
            $value = '';
        
            $valueObject = $this->option->value()[strtolower($language->code())];
        
            /** @var AbstractContentManagerOptionValue $valueObject */
            $headingsContent[] = ['languages_id' => $language->id(), 'content_heading' => ''];
        
            if ($valueObject != null) {
                $title = empty($valueObject->title()) ? $this->defaultTitle() : $valueObject->title();
                $value = $valueObject->value();
            }
        
            $titlesContent[] = ['languages_id' => $language->id(), 'content_title' => $title];
            $textsContent[]  = ['languages_id' => $language->id(), 'content_text' => $value];
            $statusContent[]  = ['languages_id' => $language->id(), 'content_status' => '1'];
        }
    
        $headings = $this->contentValueObjectFactory->createContentHeadingCollection($headingsContent);
        $texts    = $this->contentValueObjectFactory->createContentTextCollection($textsContent);
        $titles   = $this->contentValueObjectFactory->createContentTitleCollection($titlesContent);
        $statuses = $this->contentValueObjectFactory->createContentStatusCollection($statusContent);
    
        if ($this->option->contentIdentification() === null) {
            /**
             * @var ContentIdentificationInterface $identification
             */
            $this->option->setContentIdentification($this->contentReadService->nextContentGroupId());
        }
    
        if (empty($this->option->contentIdentification()->contentAlias())) {
            //create an ID with the currentTheme
            $this->option->setContentIdentification($this->option->contentIdentification()
                                                        ->forTheme($this->currentTheme->id()));
        }
    
        return InfoElementContentBuilder::create()
            ->usingId($this->option->contentIdentification())
            ->usingStatus($statuses)
            ->inPosition(ElementPosition::createForStyleEdit())
            ->usingHeadings($headings)
            ->usingTexts($texts)
            ->usingTitles($titles)
            ->build();
    }
}
