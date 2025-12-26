<?php

/* --------------------------------------------------------------
   StaticSeoUrlRepositoryReader.inc.php 2018-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('StaticSeoUrlRepositoryReaderInterface');

/**
 * Class StaticSeoUrlRepositoryReader
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Repositories
 */
class StaticSeoUrlRepositoryReader implements StaticSeoUrlRepositoryReaderInterface
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
     * StaticSeoUrlRepositoryReader constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns a StaticSeoUrl instance by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlInterface
     * @throws UnexpectedValueException if no staticSeoUrl record for the provided staticSeoUrl ID was found.
     *
     */
    public function getById(IdType $staticSeoUrlId)
    {
        $staticSeoUrlData = $this->db->get_where($this->table, ['static_seo_url_id' => $staticSeoUrlId->asInt()])
            ->row_array();
        
        if ($staticSeoUrlData === null) {
            throw new UnexpectedValueException('The requested staticSeoUrl was not found in database (ID:'
                                               . $staticSeoUrlId->asInt() . ')');
        }
        
        $staticSeoUrl = MainFactory::create('StaticSeoUrl');
        $this->_setDbValues($staticSeoUrl, $staticSeoUrlData);
        
        return $staticSeoUrl;
    }
    
    
    /**
     * Returns a StaticSeoUrlCollection with all existing StaticSeoUrl objects.
     *
     * @return StaticSeoUrlCollection
     */
    public function getAll()
    {
        $staticSeoUrlArray = [];
        
        $result = $this->db->get($this->table)->result_array();
        
        foreach ($result as $row) {
            $staticSeoUrl = MainFactory::create('StaticSeoUrl');
            
            $this->_setDbValues($staticSeoUrl, $row);
            
            $staticSeoUrlArray[] = $staticSeoUrl;
        }
        
        return MainFactory::create('StaticSeoUrlCollection', $staticSeoUrlArray);
    }
    
    
    /**
     * Assign the StaticSeoUrl values via the setter.
     *
     * @param StaticSeoUrl $staticSeoUrl
     * @param array        $row
     *
     * @throws InvalidArgumentException If $row contains invalid values.
     */
    protected function _setDbValues(StaticSeoUrl $staticSeoUrl, array $row)
    {
        $staticSeoUrl->setId(new IdType($row['static_seo_url_id']))
            ->setName(new StringType($row['name']))
            ->setIsInSitemapEntry(new BoolType($row['sitemap_entry']))
            ->setChangeFrequency(new StringType($row['changefreq']))
            ->setPriority(new StringType($row['priority']))
            ->setIsInRobotsFile(new BoolType($row['robots_disallow_entry']))
            ->setOpenGraphImage(new FilenameStringType((string)$row['opengraph_image']));
    }
}
