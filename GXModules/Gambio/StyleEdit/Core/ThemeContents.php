<?php

/* --------------------------------------------------------------
   ThemeContents.inc.php 2019-04-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeContents
 *
 * This class represents a theme contents value object
 */
class ThemeContents
{
    /**
     * Info page content collection
     *
     * @var InfoPageContentCollection
     */
    private $infoPageContents;
    
    /**
     * Link page content collection
     *
     * @var LinkPageContentCollection
     */
    private $linkPageContents;
    
    /**
     * Info element content collection
     *
     * @var InfoElementContentCollection
     */
    private $infoElementContents;
    
    
    /**
     * ThemeContents constructor
     *
     * @param InfoPageContentCollection    $infoPageContents    Info page contents
     * @param LinkPageContentCollection    $linkPageContents    Link page contents
     * @param InfoElementContentCollection $infoElementContents Info element contents
     */
    public function __construct(
        InfoPageContentCollection $infoPageContents,
        LinkPageContentCollection $linkPageContents,
        InfoElementContentCollection $infoElementContents
    ) {
        $this->infoPageContents    = $infoPageContents;
        $this->linkPageContents    = $linkPageContents;
        $this->infoElementContents = $infoElementContents;
    }
    
    
    /**
     * Return the info page content collection
     *
     * @return InfoPageContentCollection
     */
    public function infoPages(): InfoPageContentCollection
    {
        return $this->infoPageContents;
    }
    
    
    /**
     * Return the link page content collection
     *
     * @return LinkPageContentCollection
     */
    public function linkPages(): LinkPageContentCollection
    {
        return $this->linkPageContents;
    }
    
    
    /**
     * Return the info element content collection
     *
     * @return InfoElementContentCollection
     */
    public function infoElements(): InfoElementContentCollection
    {
        return $this->infoElementContents;
    }
}