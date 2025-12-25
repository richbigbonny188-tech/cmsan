<?php

/* --------------------------------------------------------------
   StaticSeoUrlRepositoryDeleter.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('StaticSeoUrlRepositoryDeleterInterface');

/**
 * Class StaticSeoUrlRepositoryDeleter
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Repositories
 */
class StaticSeoUrlRepositoryDeleter implements StaticSeoUrlRepositoryDeleterInterface
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
     * StaticSeoUrlRepositoryDeleter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Deletes a StaticSeoUrl by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $staticSeoUrlId)
    {
        $this->db->delete($this->table, ['static_seo_url_id' => $staticSeoUrlId->asInt()]);
        
        return $this;
    }
}