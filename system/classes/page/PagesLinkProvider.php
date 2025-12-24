<?php
/*--------------------------------------------------------------------------------------------------
    PagesLinkProvider.php 2021-08-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

class PagesLinkProvider
{
    /**
     * @var GMSEOBoost
     */
    protected $seoBoost;
    
    
    /**
     * @param GMSEOBoost|null $seoBoost
     */
    public function __construct(?GMSEOBoost $seoBoost = null)
    {
        $this->seoBoost = $seoBoost ?? GMSEOBoost::get_instance();
    }
    
    
    /**
     * @param $contentGroupId
     *
     * @return string
     */
    public function getPageContentLink($contentGroupId): string
    {
        if ($this->seoBoost->boost_content) {
            $contactUrl = xtc_href_link($this->seoBoost->get_boosted_content_url($this->seoBoost->get_content_id_by_content_group($contentGroupId)));
        } else {
            $contactUrl = xtc_href_link(FILENAME_CONTENT, "coID={$contentGroupId}");
        }
    
        return $contactUrl;
    }
}
