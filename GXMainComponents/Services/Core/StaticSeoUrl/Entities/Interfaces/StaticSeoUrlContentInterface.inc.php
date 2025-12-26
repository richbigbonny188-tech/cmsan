<?php

/* --------------------------------------------------------------
   StaticSeoUrlContentInterface.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlContentInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlContentInterface
{
    /**
     * Set the staticSeoUrlContent ID for the staticSeoUrlContent.
     *
     * @param IdType $id staticSeoUrlContent ID.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Return the staticSeoUrlContent ID.
     *
     * @return int staticSeoUrlContent ID.
     */
    public function getId();
    
    
    /**
     * Set the language ID for the staticSeoUrlContent.
     *
     * @param IdType $languageId Language ID.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setLanguageId(IdType $languageId);
    
    
    /**
     * Return the language ID.
     *
     * @return int Language ID.
     */
    public function getLanguageId();
    
    
    /**
     * Set the title for the staticSeoUrlContent.
     *
     * @param StringType $title Title for the staticSeoUrlContent.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setTitle(StringType $title);
    
    
    /**
     * Return the title of the staticSeoUrlContent.
     *
     * @return string Title.
     */
    public function getTitle();
    
    
    /**
     * Set the description for the staticSeoUrlContent.
     *
     * @param StringType $description Description for the staticSeoUrlContent.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setDescription(StringType $description);
    
    
    /**
     * Return the description of the staticSeoUrlContent.
     *
     * @return string Description.
     */
    public function getDescription();
    
    
    /**
     * Set the keywords for the staticSeoUrlContent.
     *
     * @param StringType $keywords Keywords for the staticSeoUrlContent.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setKeywords(StringType $keywords);
    
    
    /**
     * Return the keywords of the staticSeoUrlContent.
     *
     * @return string Keywords.
     */
    public function getKeywords();
}