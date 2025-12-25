<?php
/* --------------------------------------------------------------
   ListingItemMeta.php 2022-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemMeta
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemMeta
{
    private string                 $title;
    private string                 $description;
    private string                 $keywords;
    private string                 $link;
    private ListingItemMetaSitemap $sitemap;
    
    
    /**
     * ListingItemMeta constructor.
     *
     * @param string                 $title
     * @param string                 $description
     * @param string                 $keywords
     * @param string                 $link
     * @param ListingItemMetaSitemap $sitemap
     */
    public function __construct(
        string                 $title,
        string                 $description,
        string                 $keywords,
        string                 $link,
        ListingItemMetaSitemap $sitemap
    ) {
        $this->title       = $title;
        $this->description = $description;
        $this->keywords    = $keywords;
        $this->link        = $link;
        $this->sitemap     = $sitemap;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title'       => $this->title,
            'description' => $this->description,
            'keywords'    => $this->keywords,
            'link'        => $this->link,
            'sitemap'     => $this->sitemap->toArray(),
        ];
    }
}