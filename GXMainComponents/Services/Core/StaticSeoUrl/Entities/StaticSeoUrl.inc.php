<?php

/* --------------------------------------------------------------
   StaticSeoUrl.inc.php 2018-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Class StaticSeoUrl
 *
 * Represents the default settings of a static seo url. Has got the static seo url contents as collection.
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Entities
 */
class StaticSeoUrl implements StaticSeoUrlInterface
{
    /**
     * StaticSeoUrl ID
     *
     * @var int
     */
    protected $id = 0;
    
    /**
     * StaticSeoUrl Name
     *
     * @var string
     */
    protected $name = '';
    
    /**
     * Is it shown in sitemap?
     *
     * @var bool
     */
    protected $sitemapEntry = false;
    
    /**
     * Change frequency for sitemap
     *
     * @var double
     */
    protected $changeFrequency = '';
    
    /**
     * Priority for sitemap
     *
     * @var double
     */
    protected $priority = '';
    
    /**
     * Is it shown in robots file?
     *
     * @var bool
     */
    protected $robotsEntry = false;
    
    /**
     * Image for OpenGraph page property
     *
     * @var string
     */
    protected $openGraphImage;
    
    /**
     * Collection of staticSeoUrlContents
     *
     * @var StaticSeoUrlContentCollection
     */
    protected $staticSeoUrlContentCollection;
    
    
    public function __construct()
    {
        $this->openGraphImage = MainFactory::create('FilenameStringType', '');
        // Set StaticSeoUrlContent items.
        $this->setStaticSeoUrlContentCollection(MainFactory::create('StaticSeoUrlContentCollection', []));
    }
    
    
    /**
     * Set the ID for the staticSeoUrl.
     *
     * @param IdType $id StaticSeoUrl ID.
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the ID of the staticSeoUrl.
     *
     * @return int StaticSeoUrl ID.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Set the name for the staticSeoUrl.
     *
     * @param StringType $name Name for the staticSeoUrl.
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setName(StringType $name)
    {
        $this->name = $name->asString();
        
        return $this;
    }
    
    
    /**
     * Return the Name of the staticSeoUrl.
     *
     * @return string Name.
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Return true when the staticSeoUrl should be considered in the sitemap, false otherwise.
     *
     * @return bool
     */
    public function isInSitemapEntry()
    {
        return $this->sitemapEntry;
    }
    
    
    /**
     * Consider the staticSeoUrl in the sitemap or not.
     *
     * @param BoolType $status True when it should be displayed, false otherwise.
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setIsInSitemapEntry(BoolType $status)
    {
        $this->sitemapEntry = $status->asBool();
        
        return $this;
    }
    
    
    /**
     * Return the change frequency of the staticSeoUrl.
     *
     * @return string Change frequency.
     */
    public function getChangeFrequency()
    {
        return $this->changeFrequency;
    }
    
    
    /**
     * Value of change frequency of staticSeoUrl in sitemap
     *
     * @param StringType $changeFrequency
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setChangeFrequency(StringType $changeFrequency)
    {
        $this->changeFrequency = $changeFrequency->asString();
        
        return $this;
    }
    
    
    /**
     * Return the Priority of the staticSeoUrl.
     *
     * @return string Priority.
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    
    /**
     * Value of Priority of staticSeoUrl in sitemap
     *
     * @param StringType $priority
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setPriority(StringType $priority)
    {
        $this->priority = $priority->asString();
        
        return $this;
    }
    
    
    /**
     * Return true when the staticSeoUrl should be considered in the robots file, false otherwise.
     *
     * @return bool
     */
    public function isInRobotsFile()
    {
        return $this->robotsEntry;
    }
    
    
    /**
     * Consider the staticSeoUrl in the robots file or not.
     *
     * @param BoolType $status True when it should be displayed, false otherwise.
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setIsInRobotsFile(BoolType $status)
    {
        $this->robotsEntry = $status->asBool();
        
        return $this;
    }
    
    
    /**
     * Set the static seo url content collection for the static seo url.
     *
     * @param StaticSeoUrlContentCollection $staticSeoUrlContentCollection
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setStaticSeoUrlContentCollection(StaticSeoUrlContentCollection $staticSeoUrlContentCollection)
    {
        $this->staticSeoUrlContentCollection = $staticSeoUrlContentCollection;
        
        return $this;
    }
    
    
    /**
     * Return the staticSeoUrlContent collection of the staticSeoUrl.
     *
     * @return StaticSeoUrlContentCollection StaticSeoUrlContent collection.
     */
    public function getStaticSeoUrlContentCollection()
    {
        return $this->staticSeoUrlContentCollection;
    }
    
    
    /**
     * Adds a staticSeoUrlContent to the staticSeoUrlContent collection
     *
     * @param StaticSeoUrlContentInterface $staticSeoUrlContent
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    public function addStaticSeoUrlContent(StaticSeoUrlContentInterface $staticSeoUrlContent)
    {
        $this->staticSeoUrlContentCollection->addItem($staticSeoUrlContent);
        
        return $this;
    }
    
    
    /**
     * @return string
     */
    public function getOpenGraphImage()
    {
        return $this->openGraphImage;
    }
    
    
    /**
     * @param \FilenameStringType $openGraphImage
     *
     * @return \StaticSeoUrl
     */
    public function setOpenGraphImage(FilenameStringType $openGraphImage)
    {
        $this->openGraphImage = $openGraphImage;
        
        return $this;
    }
}
