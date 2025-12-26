<?php
/* --------------------------------------------------------------
   ThemeContentsParser.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeContentsParser
 *
 * This class represents a parser for theme contents
 */
class ThemeContentsParser
{
    protected static $identificationFactory;
    
    
    /**
     * Return the parsed theme contents
     *
     * @param stdClass $contents Theme contents
     *
     * @return ThemeContents
     * @throws UnfinishedBuildException
     */
    public static function parse(stdClass $contents)
    {
        $properties = ['infoPages', 'linkPages', 'infoElements'];
        
        //  Ensuring that if a property is not send or needed the collection can be constructed
        foreach ($properties as $property) {
            if (!property_exists($contents, $property)) {
                $contents->$property = [];
            }
        }
        
        $infoPageContents    = self::parseInfoPageContents($contents->infoPages);
        $linkPageContents    = self::parseLinkPageContents($contents->linkPages);
        $infoElementContents = self::parseInfoElementContents($contents->infoElements);
        
        return new ThemeContents($infoPageContents, $linkPageContents, $infoElementContents);
    }
    
    
    /**
     * Return the parsed info page contents
     *
     * @param array $infoPageContents Info page contents
     *
     * @return InfoPageContentCollection
     * @throws UnfinishedBuildException
     */
    protected static function parseInfoPageContents(array $infoPageContents)
    {
        $contents = [];
        
        foreach ($infoPageContents as $infoPageContent) {
            $contents[] = self::parseInfoPageContent($infoPageContent);
        }
        
        return new InfoPageContentCollection($contents);
    }
    
    
    /**
     * Return the parsed link page contents
     *
     * @param array $linkPageContents Link page contents
     *
     * @return LinkPageContentCollection
     * @throws UnfinishedBuildException
     */
    protected static function parseLinkPageContents(array $linkPageContents)
    {
        $contents = [];
        
        foreach ($linkPageContents as $linkPageContent) {
            $contents[] = self::parseLinkPageContent($linkPageContent);
        }
        
        return new LinkPageContentCollection($contents);
    }
    
    
    /**
     * Return the parsed info element contents
     *
     * @param array $infoElementContents Info element contents
     *
     * @return InfoElementContentCollection
     * @throws UnfinishedBuildException
     */
    protected static function parseInfoElementContents(array $infoElementContents)
    {
        $contents = [];
        
        foreach ($infoElementContents as $infoElementContent) {
            $contents[] = self::parseInfoElementContent($infoElementContent);
        }
        
        return new InfoElementContentCollection($contents);
    }
    
    
    /**
     * Return a parsed info page content
     *
     * @param stdClass $content Info page content
     *
     * @return InfoPageContent
     */
    protected static function parseInfoPageContent(stdClass $content): \InfoPageContent
    {
        if ($content->type !== ContentType::CONTENT) {
            throw new ParseError('Content type does not match');
        }
        
        /**/
        $position        = PagePosition::createFromString($content->position);
        $contentGroup    = $content->id ?? null;
        $contentAlias    = $content->alias ?? null;
        $contentId       = self::identificationFactory()->forPreference($contentAlias, $contentGroup);
        $localizedStatus = self::createClassForLocalizedCollection($content->status);
        $status          = self::createLocalizedCollection($localizedStatus, new StringType(ContentStatus::class));
        $names           = self::createLocalizedCollection($content->name, new StringType(ContentName::class));
        $titles          = self::createLocalizedCollection($content->title, new StringType(ContentTitle::class));
        $headings        = self::createLocalizedCollection($content->heading, new StringType(ContentHeading::class));
        $texts           = self::createLocalizedCollection($content->text, new StringType(ContentText::class));
        $downloads       = self::createLocalizedCollection($content->downloadFile,
                                                           new StringType(ContentDownloadFile::class));
        $metaTitles      = self::createLocalizedCollection($content->metaTitle,
                                                           new StringType(ContentMetaTitle::class));
        $metaKeywords    = self::createLocalizedCollection($content->metaKeywords,
                                                           new StringType(ContentMetaKeywords::class));
        $metaDescription = self::createLocalizedCollection($content->metaDescription,
                                                           new StringType(ContentMetaDescription::class));
        $urlKeywords     = self::createLocalizedCollection($content->urlKeywords,
                                                           new StringType(ContentUrlKeywords::class));
        $urlRewrites     = self::createLocalizedCollection($content->urlRewrite,
                                                           new StringType(ContentUrlRewrite::class));
        
        $sitemaps = null;
        if (null !== $content->sitemap) {
            $sitemaps = [];
            foreach ($content->sitemap as $langCode => $item) {
                $languageCode = new LanguageCode(new StringType($langCode));
            
                $sitemaps[] = new ContentSitemap(new ContentSitemapVisibility((bool)$item->gmPriority),
                                                 new ContentSitemapPriority((float)$item->gmSitemapEntry),
                                                 ContentSitemapChangeFrequency::createFromString($item->gmChangeFreq ? : ContentSitemapChangeFrequency::ALWAYS),
                                                 $languageCode);
            }
        
            $sitemaps = new ContentSitemapCollection($sitemaps);
        }
    
        $allowRobots         = self::createClassForLocalizedCollection($content->allowRobots ?? false);
        $allowRobotsStatuses = self::createLocalizedCollection($allowRobots,
                                                               new StringType(ContentAllowRobotsStatus::class));
        $opengraphImages     = self::createLocalizedCollection($content->opengraphImage,
                                                               new StringType(ContentOpengraphImage::class));
        
        return ContentFactory::newInfoPageContent()
            ->inPosition($position)
            ->usingId($contentId)
            ->usingStatus($status)
            ->usingNames($names)
            ->usingTitles($titles)
            ->usingHeadings($headings)
            ->usingTexts($texts)
            ->usingDownloadFiles($downloads)
            ->usingMetaTitles($metaTitles)
            ->usingMetaKeywords($metaKeywords)
            ->usingMetaDescriptions($metaDescription)
            ->usingUrlKeywords($urlKeywords)
            ->usingUrlRewrites($urlRewrites)
            ->usingSitemaps($sitemaps)
            ->usingAllowRobotsStatuses($allowRobotsStatuses)
            ->usingOpengraphImages($opengraphImages)
            ->build();
    }
    
    
    /**
     * Return a parsed link page content
     *
     * @param stdClass $content Link page content
     *
     * @return LinkPageContent
     * @throws UnfinishedBuildException
     */
    protected static function parseLinkPageContent(stdClass $content)
    {
        if ($content->type !== ContentType::LINK) {
            throw new ParseError('Content type does not match');
        }
    
        $position           = PagePosition::createFromString($content->position);
        $localizedStatus    = self::createClassForLocalizedCollection($content->status);
        $openInNewTab       = self::createClassForLocalizedCollection($content->openInNewTab);
        $status             = self::createLocalizedCollection($localizedStatus, new StringType(ContentStatus::class));
        $names              = self::createLocalizedCollection($content->name, new StringType(ContentName::class));
        $titles             = self::createLocalizedCollection($content->title, new StringType(ContentTitle::class));
        $links              = self::createLocalizedCollection($content->link, new StringType(ContentLink::class));
        $contentGroup       = $content->id ?? null;
        $contentAlias       = $content->alias ?? null;
        $contentId          = self::identificationFactory()->forPreference($contentAlias, $contentGroup);
        $openInNewTabStatus = self::createLocalizedCollection($openInNewTab,
                                                              new StringType(ContentOpenInNewTabStatus::class));
        
        return ContentFactory::newLinkPageContent()
            ->inPosition($position)
            ->usingId($contentId)
            ->usingStatus($status)
            ->usingNames($names)
            ->usingTitles($titles)
            ->usingLinks($links)
            ->usingOpenInNewTabStatus($openInNewTabStatus)
            ->build();
    }
    
    
    /**
     * Return a parsed info element content
     *
     * @param stdClass $content Info element content
     *
     * @return InfoElementContent
     * @throws UnfinishedBuildException
     */
    protected static function parseInfoElementContent(stdClass $content)
    {
        if ($content->type !== ContentType::CONTENT) {
            throw new ParseError('Content type does not match');
        }
        
        $position = ElementPosition::createFromString($content->position);
    
        $localizedStatus = self::createClassForLocalizedCollection($content->status);
        $status          = self::createLocalizedCollection($localizedStatus, new StringType(ContentStatus::class));
        $titles          = self::createLocalizedCollection($content->title, new StringType(ContentTitle::class));
        $headings        = self::createLocalizedCollection($content->heading, new StringType(ContentHeading::class));
        $texts           = self::createLocalizedCollection($content->text, new StringType(ContentText::class));
        $contentGroup    = $content->id ?? null;
        $contentAlias    = $content->alias ?? null;
        $contentId       = self::identificationFactory()->forPreference($contentAlias, $contentGroup);
        return ContentFactory::newInfoElementContent()
            ->inPosition($position)
            ->usingId($contentId)
            ->usingStatus($status)
            ->usingTitles($titles)
            ->usingHeadings($headings)
            ->usingTexts($texts)
            ->build();
    }
    
    
    /**
     *
     */
    protected static function identificationFactory(): ContentIdentificationFactoryInterface
    {
        if (static::$identificationFactory === null) {
            static::$identificationFactory = MainFactory::create(ContentIdentificationFactory::class);
        }
        
        return static::$identificationFactory;
    }
    
    
    /**
     * Return a collection of the provided localized values
     *
     * @param stdClass    $values        Object containing locale-based values
     * @param \StringType $itemClassName Item class name
     *
     * @return mixed Dynamic output (all outputs implement LocalizedContentAttributeCollectionInterface)
     */
    public static function createLocalizedCollection(
        stdClass $values = null,
        StringType $itemClassName
    ) {
        if ($values === null) {
            return null;
        }
        
        $items               = [];
        $collectionClassName = $itemClassName->asString() . 'Collection';
        
        foreach (get_object_vars($values) as $languageCode => $name) {
            if (in_array($itemClassName->asString(), ['ContentAllowRobotsStatus', 'ContentOpenInNewTabStatus'])) {
                $firstParameter = (bool)($name);
            } else {
                $firstParameter = (string)$name;
            }
            
            $languageCodeObject = MainFactory::create('LanguageCode', new StringType($languageCode));
            $items[]            = MainFactory::create($itemClassName->asString(), $firstParameter, $languageCodeObject);
        }
        
        return MainFactory::create($collectionClassName, $items);
    }
    
    
    /**
     * Converts the value to language specific
     *
     * @param $value
     *
     * @return stdClass
     */
    protected static function createClassForLocalizedCollection($value): stdClass
    {
        if ($value instanceof stdClass) {
            return $value;
        }
        /** @var LanguagesReadService $langReadService */
        $langReadService = StaticGXCoreLoader::getService('LanguagesRead');
        $result          = [];
        foreach ($langReadService->getLanguages() as $langCode) {
            $result[$langCode->code()] = $value;
        }
        
        return (object)$result;
    }
}
