<?php
/* --------------------------------------------------------------
   InfoElementContentBuilder.inc.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoElementContentBuilder
 *
 * This class represents an info element content builder
 *
 * @category   System
 * @package    Content
 */
class InfoElementContentBuilder implements BuilderInterface, InfoElementContentBuilderInterface
{
    /**
     * Content position
     *
     * @var PagePosition
     */
    protected $position;
    
    /**
     * Content status
     *
     * @var ContentStatusCollection
     */
    protected $status;
    
    /**
     * Localized content titles
     *
     * @var ContentTitleCollection
     */
    protected $titles;
    
    /**
     * Localized content headings
     *
     * @var ContentHeadingCollection
     */
    protected $headings;
    
    /**
     * Localized content texts
     *
     * @var ContentTextCollection
     */
    protected $texts;
    
    /**
     * Sort order
     *
     * @var ContentSortOrder
     */
    protected $sortOrder;
    
    /**
     * Content group id
     *
     * @var int|null
     */
    protected $id = null;
    
    
    /**
     * Return new instance of the builder
     *
     * @return InfoElementContentBuilder New instance
     */
    public static function create(): self
    {
        return new self;
    }
    
    
    /**
     * Return the created InfoElementContent instance
     *
     * @return InfoElementContent Info element content entity object
     *
     * @throws UnfinishedBuildException On calling build() before having all properties set
     */
    public function build(): InfoElementContent
    {
        $properties = [
            'position'  => $this->position,
            'status'    => $this->status,
            'titles'    => $this->titles,
            'headings'  => $this->headings,
            'texts'     => $this->texts,
            'id'        => $this->id,
            'sortOrder' => $this->sortOrder,
        ];
        
        foreach ($properties as $name => $value) {
            if (null === $value && $name !== 'id' && $name !== 'sortOrder') {
                throw new UnfinishedBuildException("Property {$name} is not set");
            }
        }
        
        return new InfoElementContent(...array_values($properties));
    }
    
    
    /**
     * Set the page position
     *
     * @param ElementPosition $position Element position
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function inPosition(ElementPosition $position): InfoElementContentBuilder
    {
        $this->position = $position;
        
        return $this;
    }
    
    
    /**
     * Set the activation status
     *
     * @param ContentStatusCollection $status Activation status
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingStatus(ContentStatusCollection $status): InfoElementContentBuilder
    {
        $this->status = $status;
        
        return $this;
    }
    
    
    /**
     * Set the localized content titles
     *
     * @param ContentTitleCollection $titles Localized titles
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingTitles(ContentTitleCollection $titles): InfoElementContentBuilder
    {
        $this->titles = $titles;
        
        return $this;
    }
    
    
    /**
     * Set the localized content headings
     *
     * @param ContentHeadingCollection $headings Localized headings
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingHeadings(ContentHeadingCollection $headings): InfoElementContentBuilder
    {
        $this->headings = $headings;
        
        return $this;
    }
    
    
    /**
     * Set the localized content texts
     *
     * @param ContentTextCollection $texts Localized texts
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingTexts(ContentTextCollection $texts): InfoElementContentBuilder
    {
        $this->texts = $texts;
        
        return $this;
    }
    
    
    /**
     * Set the sort order
     *
     * @param ContentSortOrderInterface $sortOrder
     *
     * @return $this
     */
    public function usingSortOrder(ContentSortOrderInterface $sortOrder): InfoElementContentBuilder
    {
        $this->sortOrder = $sortOrder;
        
        return $this;
    }
    
    
    /**
     * Set the content group id
     *
     * @param ContentIdentificationInterface $id
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingId(ContentIdentificationInterface $id = null): InfoElementContentBuilder
    {
        $this->id = $id;
        
        return $this;
    }
}
