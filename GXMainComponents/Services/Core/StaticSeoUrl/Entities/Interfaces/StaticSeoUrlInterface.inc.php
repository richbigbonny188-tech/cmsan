<?php

/* --------------------------------------------------------------
   StaticSeoUrlInterface.inc.php 2018-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlInterface
{
    /**
     * Set the ID for the staticSeoUrl.
     *
     * @param IdType $id StaticSeoUrl ID.
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Return the ID of the staticSeoUrl.
     *
     * @return int StaticSeoUrl ID.
     */
    public function getId();
    
    
    /**
     * Set the name for the staticSeoUrl.
     *
     * @param StringType $name Name for the staticSeoUrl.
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setName(StringType $name);
    
    
    /**
     * Return the Name of the staticSeoUrl.
     *
     * @return string Name.
     */
    public function getName();
    
    
    /**
     * Return true when the staticSeoUrl should be considered in the sitemap, false otherwise.
     *
     * @return bool
     */
    public function isInSitemapEntry();
    
    
    /**
     * Consider the staticSeoUrl in the sitemap or not.
     *
     * @param BoolType $status True when it should be displayed, false otherwise.
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setIsInSitemapEntry(BoolType $status);
    
    
    /**
     * Return the change frequency of the staticSeoUrl.
     *
     * @return string Change frequency.
     */
    public function getChangeFrequency();
    
    
    /**
     * Value of change frequency of staticSeoUrl in sitemap
     *
     * @param StringType $changeFrequency
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setChangeFrequency(StringType $changeFrequency);
    
    
    /**
     * Return the Priority of the staticSeoUrl.
     *
     * @return string Priority.
     */
    public function getPriority();
    
    
    /**
     * Value of Priority of staticSeoUrl in sitemap
     *
     * @param StringType $priority
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setPriority(StringType $priority);
    
    
    /**
     * Return true when the staticSeoUrl should be considered in the robots file, false otherwise.
     *
     * @return bool
     */
    public function isInRobotsFile();
    
    
    /**
     * Consider the staticSeoUrl in the robots file or not.
     *
     * @param BoolType $status True when it should be displayed, false otherwise.
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setIsInRobotsFile(BoolType $status);
    
    
    /**
     * Set the staticSeoUrlContentCollection for the staticSeoUrl.
     *
     * @param StaticSeoUrlContentCollection $staticSeoUrlContentCollection
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     */
    public function setStaticSeoUrlContentCollection(StaticSeoUrlContentCollection $staticSeoUrlContentCollection);
    
    
    /**
     * Return the staticSeoUrlContentCollection of the staticSeoUrl.
     *
     * @return StaticSeoUrlContentCollection staticSeoUrlContent collection.
     */
    public function getStaticSeoUrlContentCollection();
    
    
    /**
     * Adds a staticSeoUrlContent to the staticSeoUrlContentCollection
     *
     * @param StaticSeoUrlContentInterface $staticSeoUrlContentInterface
     *
     * @return StaticSeoUrlInterface Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    public function addStaticSeoUrlContent(StaticSeoUrlContentInterface $staticSeoUrlContentInterface);
    
    
    /**
     * Returns name of OpenGraph image file
     *
     * @return \FilenameStringType;
     */
    public function getOpenGraphImage();
    
    
    /**
     * @param \FilenameStringType $openGraphImage
     *
     * @return \StaticSeoUrlInterface
     */
    public function setOpenGraphImage(FilenameStringType $openGraphImage);
    
}
