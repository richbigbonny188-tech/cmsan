<?php
/* --------------------------------------------------------------
   ContentRepository.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentRepository
 *
 * This class represents the content repository
 *
 * @category   System
 * @package    Content
 */
class ContentRepository implements ContentRepositoryInterface
{
    /**
     * Content writer
     *
     * @var ContentWriter
     */
    protected $writer;
    
    /**
     * Content reader
     *
     * @var ContentReaderInterface
     */
    protected $reader;
    
    /**
     * Content deleter
     *
     * @var ContentDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var ContentValueObjectFactory
     */
    protected $factory;
    /**
     * @var ContentIdentificationFactoryInterface
     */
    protected $contentIdentificationFactory;
    
    
    /**
     * ContentRepository constructor
     *
     * @param ContentWriterInterface                $writer Content writer
     * @param ContentReaderInterface                $reader Content reader
     * @param ContentDeleterInterface               $deleter
     * @param ContentValueObjectFactory             $factory
     * @param ContentIdentificationFactoryInterface $contentIdentificationFactory
     */
    public function __construct(
        ContentWriterInterface $writer,
        ContentReaderInterface $reader,
        ContentDeleterInterface $deleter,
        ContentValueObjectFactory $factory,
        ContentIdentificationFactoryInterface $contentIdentificationFactory
    ) {
        $this->writer  = $writer;
        $this->reader  = $reader;
        $this->deleter = $deleter;
        $this->factory = $factory;
        $this->contentIdentificationFactory = $contentIdentificationFactory;
    }
    
    
    /**
     * Save the info page content
     *
     * @param InfoPageContent $infoPageContent Info page content
     *
     * @return ContentRepositoryInterface Same instance for chained method calls
     *
     * @throws Exception
     */
    public function storeInfoPageContent(InfoPageContent $infoPageContent): ContentRepositoryInterface
    {
        $this->writer->storeInfoPageContent($infoPageContent);
        
        return $this;
    }
    
    
    /**
     * Save the link page content
     *
     * @param LinkPageContent $linkPageContent Link page content
     *
     * @return ContentRepositoryInterface Same instance for chained method calls
     *
     * @throws Exception
     */
    public function storeLinkPageContent(LinkPageContent $linkPageContent): ContentRepositoryInterface
    {
        $this->writer->storeLinkPageContent($linkPageContent);
        
        return $this;
    }
    
    
    /**
     * Save the info element content
     *
     * @param InfoElementContent $infoElementContent Info element content
     *
     * @return ContentRepositoryInterface Same instance for chained method calls
     *
     * @throws Exception
     */
    public function storeInfoElementContent(InfoElementContent $infoElementContent): ContentRepositoryInterface
    {
        $this->writer->storeInfoElementContent($infoElementContent);
        
        return $this;
    }
    
    
    /**
     * @param mixed $id id of the Content
     *
     * @return mixed
     * @throws ContentNotFoundException
     * @throws UnfinishedBuildException
     * @throws UrlRewriteNotFoundException
     */
    public function findById(ContentIdentificationInterface $id)
    {
        $data = $this->reader->findById($id);
        
        if ($data[0]['content_type'] === 'link') {
            
            return $this->createLinkPageContent($data);
        }
        
        if ($data[0]['content_type'] === 'content' && $this->isInfoPageContent($data[0]['content_position'])) {
            
            $urlRewrites = [];
            
            foreach ($data as $item) {
                $urlRewriteItem = ['rewrite_url' => '', 'language_id' => $item['languages_id']];
                
                try {
                    $urlRewriteContent = $this->reader->findUrlRewriteByContentId($item['content_group']);
                    foreach ($urlRewriteContent as $content) {
                        if ($content['language_id'] === $item['languages_id']) {
                            $urlRewriteItem = $content;
                            break;
                        }
                    }
                    $urlRewrites[] = $urlRewriteItem;
                } catch (\Exception $exception) {
                    $urlRewrites[] = $urlRewriteItem;
                }
            }
            
            return $this->createInfoPageContent($data, $urlRewrites);
        }
        
        return $this->createInfoElementContent($data);
    }
    
    
    /**
     * @param $data
     *
     * @return LinkPageContent
     * @throws UnfinishedBuildException
     */
    protected function createLinkPageContent(array $data): LinkPageContent
    {
        $linkPageValueObjects = $this->factory->createValueObjectsForLinkPageContent($data);
        
        return LinkPageContentBuilder::create()
            ->usingNames($linkPageValueObjects['names'])
            ->inPosition($linkPageValueObjects['position'])
            ->usingTitles($linkPageValueObjects['titles'])
            ->usingLinks($linkPageValueObjects['links'])
            ->usingOpenInNewTabStatus($linkPageValueObjects['openInNewTab'])
            ->usingStatus($linkPageValueObjects['status'])
            ->usingId($this->contentIdentificationFactory->forPreference($linkPageValueObjects['alias'], $linkPageValueObjects['id']))
            ->usingSortOrder($linkPageValueObjects['sortOrder'])
            ->usingDelete($linkPageValueObjects['deletable'])
            ->build();
    }
    
    
    /**
     * @param array $data
     *
     * @return InfoElementContent
     * @throws UnfinishedBuildException
     * @throws Exception
     */
    protected function createInfoElementContent(array $data): InfoElementContent
    {
        $infoElementValueObjects = $this->factory->createValueObjectsForInfoElementContent($data);
        
        return InfoElementContentBuilder::create()
            ->inPosition($infoElementValueObjects['position'])
            ->usingHeadings($infoElementValueObjects['headings'])
            ->usingId($this->contentIdentificationFactory->forPreference($infoElementValueObjects['alias'],$infoElementValueObjects['id']))
            ->usingStatus($infoElementValueObjects['status'])
            ->usingTexts($infoElementValueObjects['texts'])
            ->usingTitles($infoElementValueObjects['titles'])
            ->build();
    }
    
    
    /**
     * @param array $data
     * @param array $urlRewriteData
     *
     * @return InfoPageContent
     * @throws Exception
     */
    protected function createInfoPageContent(array $data, array $urlRewriteData): InfoPageContent
    {
        $infoPageValueObjects = $this->factory->createValueObjectsForInfoPageContent($data, $urlRewriteData);
        
        return InfoPageContentBuilder::create()
            ->usingTitles($infoPageValueObjects['titles'])
            ->usingTexts($infoPageValueObjects['texts'])
            ->usingStatus($infoPageValueObjects['status'])
            ->usingId($this->contentIdentificationFactory->forPreference($infoPageValueObjects['alias'],$infoPageValueObjects['id']))
            ->usingHeadings($infoPageValueObjects['headings'])
            ->inPosition($infoPageValueObjects['position'])
            ->usingAllowRobotsStatuses($infoPageValueObjects['allowRobots'])
            ->usingDownloadFiles($infoPageValueObjects['downloadFiles'])
            ->usingMetaDescriptions($infoPageValueObjects['metaDescriptions'])
            ->usingMetaKeywords($infoPageValueObjects['metaKeywords'])
            ->usingMetaTitles($infoPageValueObjects['metaTitles'])
            ->usingNames($infoPageValueObjects['names'])
            ->usingOpengraphImages($infoPageValueObjects['openGraph'])
            ->usingSitemaps($infoPageValueObjects['sitemaps'])
            ->usingUrlKeywords($infoPageValueObjects['urlKeywords'])
            ->usingUrlRewrites($infoPageValueObjects['urlRewrites'])
            ->usingSortOrder($infoPageValueObjects['sortOrder'])
            ->usingDelete($infoPageValueObjects['deletable'])
            ->build();
    }
    
    
    protected function createFilePageContent(array $data, array $urlRewriteData)
    {
        $filePageValueObjects = $this->factory->createValueObjectsForFilePageContent($data, $urlRewriteData);
        
        return ScriptPageContentBuilder::create()
            ->usingNames($filePageValueObjects['names'])
            ->usingTitles($filePageValueObjects['titles'])
            ->inPosition($filePageValueObjects['position'])
            ->usingId($this->contentIdentificationFactory->forPreference($filePageValueObjects['alias'],$filePageValueObjects['id']))
            ->usingStatus($filePageValueObjects['status'])
            ->usingAllowRobotsStatus($filePageValueObjects['allowRobots'])
            ->usingMetaDescriptions($filePageValueObjects['metaDescriptions'])
            ->usingMetaKeywords($filePageValueObjects['metaKeywords'])
            ->usingMetaTitles($filePageValueObjects['metaTitles'])
            ->usingUrlKeywords($filePageValueObjects['urlKeywords'])
            ->usingUrlRewrites($filePageValueObjects['urlRewrites'])
            ->usingSitemaps($filePageValueObjects['sitemaps'])
            ->usingSortOrder($filePageValueObjects['sortOrder'])
            ->usingDelete($filePageValueObjects['deletable'])
            ->usingOpenInNewTabStatus($filePageValueObjects['openInNewTab'])
            ->usingScriptFiles($filePageValueObjects['scriptFiles'])
            ->build();
    }
    
    /**
     * Deletes the content data in database by id.
     *
     * @param ContentIdentificationInterface $Id
     *
     * @return $this|ContentRepositoryInterface Same instance for chained method calls.
     */
    public function deleteById(ContentIdentificationInterface $Id)
    {
        $this->deleter->deleteById($Id);
        
        return $this;
    }
    
    
    /**
     * Updates an info page content.
     *
     * @param InfoPageContent $infoPage
     *
     * @throws Exception
     */
    public function updateInfoPageContent(InfoPageContent $infoPage): void
    {
        $this->writer->updateInfoPageContent($infoPage);
    }
    
    
    /**
     * Updates a link page content.
     *
     * @param LinkPageContent $linkPage
     *
     * @throws Exception
     */
    public function updateLinkPageContent(LinkPageContent $linkPage): void
    {
        $this->writer->updateLinkPageContent($linkPage);
    }
    
    
    /**
     * Updates an info element content
     *
     * @param InfoElementContent $infoElement
     *
     * @throws Exception
     */
    public function updateInfoElementContent(InfoElementContent $infoElement): void
    {
        $this->writer->updateInfoElementContent($infoElement);
    }
    
    
    /**
     * @return ContentIdentificationInterface
     */
    public function nextContentGroupId(): ContentIdentificationInterface
    {
        return $this->reader->nextContentGroupId();
    }
    
    
    /**
     * @return InfoElementContent[]
     * @throws ContentNotFoundException
     * @throws UnfinishedBuildException
     */
    public function getAllInfoElements(): array
    {
        $result = $this->reader->findAllInfoElements();
        
        $contentGroupArray = $infoElements = [];
        
        // grouping all the InfoElements by its ContentGroupId
        foreach ($result as $entry) {
            
            if (!isset($contentGroupArray[$entry['content_group']])) {
                
                $contentGroupArray[$entry['content_group']] = [];
            }
            $contentGroupArray[$entry['content_group']][] = $entry;
        }
        
        if (count($contentGroupArray)) {
            
            foreach ($contentGroupArray as $infoElement) {
                
                $infoElements[] = $this->createInfoElementContent($infoElement);
            }
        }
        
        return $infoElements;
    }
    
    
    /**
     * @return InfoPageContent[]
     * @throws ContentNotFoundException
     * @throws UnfinishedBuildException
     */
    public function getAllContentPages(): array
    {
        $result = $this->reader->findAllContentPages();
        
        $contentGroupArray = $infoElements = [];
        
        // grouping all the InfoElements by its ContentGroupId
        foreach ($result as $entry) {
            
            if (!isset($contentGroupArray[$entry['content_group']])) {
                
                $contentGroupArray[$entry['content_group']] = [];
            }
            $contentGroupArray[$entry['content_group']][] = $entry;
        }
        
        if (count($contentGroupArray)) {
            
            foreach ($contentGroupArray as $infoElement) {
                $infoElements[] = $this->createPageContentFromType($infoElement);
            }
        }
        
        return $infoElements;
    }
    
    
    protected function createPageContentFromType(array $element)
    {
        $firstContent = current($element);
        
        if (in_array($firstContent['content_type'], ['content', 'file'])) {
            $urlRewrite = [];
            
            foreach ($element as $item) {
                $urlRewriteItem = ['rewrite_url' => '', 'language_id' => $item['languages_id']];
                
                try {
                    $urlRewriteContent = $this->reader->findUrlRewriteByContentId($item['content_group']);
                    foreach ($urlRewriteContent as $content) {
                        if ($content['language_id'] === $item['languages_id']) {
                            $urlRewriteItem = $content;
                            break;
                        }
                    }
                    $urlRewrite[] = $urlRewriteItem;
                } catch (\Exception $exception) {
                    $urlRewrite[] = $urlRewriteItem;
                }
            }
    
            if ($firstContent['content_type'] === 'file') {
                return $this->createFilePageContent($element, $urlRewrite);
            }
    
            return $this->createInfoPageContent($element, $urlRewrite);
        }
        
        if ($firstContent['content_type'] === 'link') {
            return $this->createLinkPageContent($element);
        }
    }
    
    
    /**
     * @param $contentPosition
     *
     * @return bool
     */
    protected function isInfoPageContent(string $contentPosition): bool
    {
        return (
            $contentPosition === 'pages_info' ||
            $contentPosition === 'pages_main' ||
            $contentPosition === 'pages_additional' ||
            $contentPosition === 'pages_secondary'
        );
    }
}
