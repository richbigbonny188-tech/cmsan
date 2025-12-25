<?php

/* --------------------------------------------------------------
   StaticSeoUrlRepositoryWriter.inc.php 2018-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('StaticSeoUrlRepositoryWriterInterface');

/**
 * Class StaticSeoUrlRepositoryWriter
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Repositories
 */
class StaticSeoUrlRepositoryWriter implements StaticSeoUrlRepositoryWriterInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'static_seo_urls';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * StaticSeoUrlRepositoryWriter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Inserts a staticSeoUrl to the database.
     *
     * @param StaticSeoUrlInterface $staticSeoUrl
     *
     * @return int ID of inserted staticSeoUrl or the given staticSeoUrl ID if the staticSeoUrl had an ID already.
     */
    public function store(StaticSeoUrlInterface $staticSeoUrl)
    {
        $sqlData = [
            'name'                  => $staticSeoUrl->getName(),
            'sitemap_entry'         => (int)$staticSeoUrl->isInSitemapEntry(),
            'changefreq'            => $staticSeoUrl->getChangeFrequency(),
            'priority'              => $staticSeoUrl->getPriority(),
            'robots_disallow_entry' => (int)$staticSeoUrl->isInRobotsFile(),
            'opengraph_image'       => $staticSeoUrl->getOpenGraphImage()->asString(),
        ];
        
        $staticSeoUrlId = $staticSeoUrl->getId();
        if ($staticSeoUrlId !== 0) {
            $this->db->update($this->table, $sqlData, ['static_seo_url_id' => $staticSeoUrlId]);
        } else {
            $this->db->insert($this->table, $sqlData);
            $staticSeoUrlId = $this->db->insert_id();
        }
        
        return $staticSeoUrlId;
    }
}
