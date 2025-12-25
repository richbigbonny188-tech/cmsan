<?php
/* --------------------------------------------------------------
  StyleEditInfoElementFactory.php 2021-05-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Factories;

use ContentIdentification;
use ContentIdentificationInterface;
use ContentStatus;
use ContentText;
use ContentTextCollection;
use ElementPosition;
use Exception;
use Gambio\StyleEdit\Core\Mapper\ContentGroupToContentAliasMapper;
use Gambio\StyleEdit\Core\Services\Entities\StyleEditInfoElementContent;
use Gambio\StyleEdit\Core\SingletonPrototype;
use InfoElementContent;
use LanguageCode;
use StringType;

/**
 * Class StyleEditInfoElementFactory
 * @package Gambio\StyleEdit\Core\Factories
 */
class StyleEditInfoElementFactory
{
    /**
     * @var ContentGroupToContentAliasMapper
     */
    protected $mapper;
    
    
    /**
     * StyleEditInfoElementFactory constructor.
     *
     * @param ContentGroupToContentAliasMapper $mapper
     */
    public function __construct(ContentGroupToContentAliasMapper $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * @param InfoElementContent $infoElement
     *
     * @param string[]           $newContent
     *
     * @return StyleEditInfoElementContent
     * @throws Exception
     */
    public function createWithNewContent(InfoElementContent $infoElement, array $newContent): StyleEditInfoElementContent
    {
        return SingletonPrototype::instance()->get(StyleEditInfoElementContent::class,
                                                   ElementPosition::createForStyleEdit(),
                                                   $infoElement->status(),
                                                   $infoElement->titles(),
                                                   $infoElement->headings(),
                                                   $this->createContentTextCollection($newContent),
                                                   $infoElement->id());
    }
    
    
    /**
     * @param InfoElementContent $infoElement
     *
     * @param string             $themeId
     *
     * @return StyleEditInfoElementContent
     * @throws Exception
     */
    public function createWithAlias(InfoElementContent $infoElement, string $themeId): StyleEditInfoElementContent
    {
        return SingletonPrototype::instance()->get(StyleEditInfoElementContent::class,
                                                   ElementPosition::createForStyleEdit(),
                                                   $infoElement->status(),
                                                   $infoElement->titles(),
                                                   $infoElement->headings(),
                                                   $infoElement->texts(),
                                                   $this->createContentIdentifierWithAlias($infoElement->id(),  $themeId));
    }
    
    
    /**
     * @param ContentIdentificationInterface $identification
     *
     * @param string                         $themeId
     *
     * @return ContentIdentificationInterface
     * @throws Exception
     */
    protected function createContentIdentifierWithAlias(ContentIdentificationInterface $identification, string $themeId): ContentIdentificationInterface
    {
        if ($identification->contentAlias() === null) {
            
            $identification = $identification->forTheme($themeId);
        }
        
        $alias = new ContentIdentification(null, $identification->contentAlias());
        
        $this->mapper->addAlias($identification->contentGroup(), $alias->contentAlias());
        
        return $alias;
        
    }
    
    /**
     * @param array $newContent
     *
     * @return ContentTextCollection
     */
    protected function createContentTextCollection(array $newContent): ContentTextCollection
    {
        $texts = [];
        
        foreach ($newContent as $languageCode => $content) {
            
            $texts[] = new ContentText($content, new LanguageCode(new StringType($languageCode)));
        }
        
        return new ContentTextCollection($texts);
    }
}
