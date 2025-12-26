<?php

/* --------------------------------------------------------------
   StaticSeoUrlContentRepositoryDeleter.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('StaticSeoUrlContentRepositoryDeleterInterface');

/**
 * Class StaticSeoUrlContentRepositoryDeleter
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Repositories
 */
class StaticSeoUrlContentRepositoryDeleter implements StaticSeoUrlContentRepositoryDeleterInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'static_seo_url_contents';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * StaticSeoUrlContentRepositoryDeleter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Deletes a StaticSeoUrlContent by the given staticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlContentId
     *
     * @return StaticSeoUrlContentRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $staticSeoUrlContentId)
    {
        $this->db->delete($this->table, ['static_seo_url_content_id' => $staticSeoUrlContentId->asInt()]);
        
        return $this;
    }
}