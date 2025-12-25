<?php
/* --------------------------------------------------------------
  InfoElementContentToThemeJsonConverter.php 2021-05-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\StyleEdit\Core\Mapper\Exceptions\AliasNotFoundException;

/**
 * Class InfoElementContentToThemeJsonConverter
 */
class InfoElementContentToThemeJsonConverter implements InfoElementContentToThemeJsonConverterInterface
{
    
    /**
     * @inheritDoc
     * @throws AliasNotFoundException
     */
    public function convert(InfoElementContent $content): stdClass
    {
        if ($content->id()->contentAlias() === null) {
            
            throw new AliasNotFoundException($content->id()->contentGroup());
        }
        
        return (object)[
            'alias'    => $content->id()->contentAlias(),
            'type'     => $content->type(),
            'position' => $content->position(),
            'status'   => $this->statusContent($content->status()),
            'title'    => $this->titleContent($content->titles()),
            'heading'  => $this->headingContent($content->headings()),
            'text'     => $this->textContent($content->texts())
        ];
    }
    
    
    /**
     * @param ContentHeadingCollection $headings
     *
     * @return stdClass
     */
    protected function headingContent(ContentHeadingCollection $headings): stdClass
    {
        $result = new stdClass;
    
        foreach ($headings as $heading) {
            $langCode = strtolower($heading->languageCode());
            
            /** @var ContentHeading $heading */
            $result->{$langCode} = $heading->content();
        }
        
        
        return $result;
    }
    
    
    /**
     * @param ContentTextCollection $texts
     *
     * @return stdClass
     */
    protected function textContent(ContentTextCollection $texts): stdClass
    {
        $result = new stdClass;
    
        foreach ($texts as $text) {
            $langCode = strtolower($text->languageCode());
            
            /** @var ContentText $text */
            $result->{$langCode} = $text->content();
        }
        
        
        return $result;
    }
    
    
    /**
     * @param ContentTitleCollection $titles
     *
     * @return stdClass
     */
    protected function titleContent(ContentTitleCollection $titles): stdClass
    {
        $result = new stdClass;
    
        foreach ($titles as $title) {
            $langCode = strtolower($title->languageCode());
            
            /** @var ContentTitle $title */
            $result->{$langCode} = $title->content();
        }
        
        
        return $result;
    }
    
    
    /**
     * @param ContentStatusCollection $statuses
     *
     * @return stdClass
     */
    protected function statusContent(ContentStatusCollection $statuses): string
    {
        $statusesArray = current($statuses->getArray());
    
        return $statusesArray->content();
    }
}
