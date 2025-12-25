<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerAdapter.php 2021-05-31
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Adapters;

use ContentReadServiceInterface;
use ContentWriteServiceInterface;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\StyleEdit\Adapters\Interfaces\ContentManagerAdapterInterface;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Factories\Interfaces\ContentManagerParserFactoryInterface;
use Gambio\StyleEdit\Core\Components\PageGroup\Entities\PageGroupOption;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use GXModules\Gambio\StyleEdit\Adapters\ConfigurationAdapter;
use PagePosition;

class ContentManagerAdapter implements ContentManagerAdapterInterface
{
    /**
     * @var ContentWriteServiceInterface
     */
    protected $contentWriteService;
    
    
    /**
     * @var ContentReadServiceInterface
     */
    protected $contentReadService;
    
    
    /**
     * @var ContentManagerParserFactoryInterface
     */
    protected $parserFactory;
    
    
    /**
     * @var TextManager
     */
    protected $textManager;
    
    
    /**
     * @var Language
     */
    protected $languageService;
    
    
    /**
     * @var ConfigurationAdapter
     */
    protected $configurationAdapter;
    
    
    /**
     * ContentManagerAdapter constructor.
     *
     * @param ContentReadServiceInterface          $contentReadService
     * @param ContentWriteServiceInterface         $contentWriteService
     * @param ContentManagerParserFactoryInterface $parserFactory
     * @param TextManager                          $textManager
     * @param Language                             $languageService
     */
    public function __construct(
        ContentReadServiceInterface $contentReadService,
        ContentWriteServiceInterface $contentWriteService,
        ContentManagerParserFactoryInterface $parserFactory,
        ConfigurationAdapter $configurationAdapter,
        TextManager $textManager,
        Language $languageService
    ) {
        $this->contentReadService   = $contentReadService;
        $this->contentWriteService  = $contentWriteService;
        $this->parserFactory        = $parserFactory;
        $this->textManager          = $textManager;
        $this->languageService      = $languageService;
        $this->configurationAdapter = $configurationAdapter;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllContentPages(): array
    {
        /**
         * @var \InfoPageContent[]
         */
        $contentPages        = $this->contentReadService->getAllContentPages();
        $menusName           = $this->getAvailableMenusName();
        $customerStatusCheck = $this->getCustomerStatusCheckConfiguration();
        $parsed              = [];
        
        foreach ($menusName as $menuName) {
            $pagePosition = $menuName->position();
            
            $parsed[$pagePosition] = [
                'id'    => $pagePosition,
                'type'  => 'pages',
                'title' => $this->getContentPositionDescription($pagePosition),
                'items' => []
            ];
            
            foreach ($contentPages as $contentPage) {
                if (!$contentPage || ($contentPage->position() !== $pagePosition)) {
                    continue;
                }
                
                $itemData                        = $this->parserFactory->createParserFor($contentPage)->parse();
                $itemData['customerStatusCheck'] = $customerStatusCheck;
                
                $parsed[$contentPage->position()]['items'][] = $itemData;
            }
        }
        
        $contents = [];
        foreach ($parsed as $pagesGroup) {
            $jsonObject = json_decode(json_encode($pagesGroup));
            $contents[] = PageGroupOption::createFromJsonObject($jsonObject);
        }
        
        return array_values($contents);
    }
    
    
    /**
     * Gets the position translation based on the request language
     *
     * @param string $position
     *
     * @return string
     */
    private function getContentPositionDescription(string $position): string
    {
        $languageId = $this->languageService->id();
        $section    = "content_manager";
        
        switch ($position) {
            case PagePosition::MAIN_NAVIGATION:
                $phrase = 'HEADING_MAIN_CATEGORIES';
                break;
            case PagePosition::INFO:
                $phrase = 'HEADING_INFO_PAGES';
                break;
            case PagePosition::INFO_BOX:
                $phrase = 'HEADING_INFO_BOX';
                break;
            case PagePosition::ADDITIONAL:
                $phrase = 'HEADING_ADDITIONAL';
                break;
            case PagePosition::SECONDARY_NAVIGATION:
                $phrase = 'HEADING_SECONDARY_NAVIGATION';
                break;
            default:
                $phrase = '';
                break;
        }
        
        return $this->textManager->getPhraseText($phrase, $section, $languageId);
    }
    
    
    /**
     * @return PagePosition[]
     */
    protected function getAvailableMenusName(): array
    {
        return [
            PagePosition::createForMainNavigation(),
            PagePosition::createForSecondaryNavigation(),
            PagePosition::createForInfo(),
            PagePosition::createForInfoBox(),
            PagePosition::createForAdditional(),
        ];
    }
    
    
    /**
     * Gets the GROUP_CHECK configuration from the gx_configurations table
     *
     * @return bool
     */
    protected function getCustomerStatusCheckConfiguration(): bool
    {
        $groupCheckConfiguration = $this->configurationAdapter->get('configuration/GROUP_CHECK');
        
        return $groupCheckConfiguration && $groupCheckConfiguration->value() === 'true';
    }
}
