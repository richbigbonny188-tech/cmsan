<?php
/* --------------------------------------------------------------
   ContentSitemapVisibility.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentSitemapVisibility
 *
 * This class represents the site map content visibility
 *
 * @category   System
 * @package    Content
 */
class ContentSitemapVisibility
{
    /**
     * Site map content visibility
     * @var bool
     */
    protected $visible;
    
    
    /**
     * ContentSitemapVisibility constructor
     *
     * @param bool $visible Site map content visibility
     */
    public function __construct(bool $visible)
    {
        $this->visible = $visible;
    }
    
    
    /**
     * Return whether the site map content is visible
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }
}