<?php

/* --------------------------------------------------------------
   StaticSeoUrlContentRepositoryWriter.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('StaticSeoUrlContentRepositoryWriterInterface');

/**
 * Class StaticSeoUrlContentRepositoryWriter
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Repositories
 */
class StaticSeoUrlContentRepositoryWriter implements StaticSeoUrlContentRepositoryWriterInterface
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
     * StaticSeoUrlContentRepositoryWriter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Inserts a staticSeoUrlContent to the database.
     *
     * @param IdType                       $staticSeoUrlId
     * @param StaticSeoUrlContentInterface $staticSeoUrlContent
     *
     * @return int ID of inserted staticSeoUrlContent or the given staticSeoUrlContent ID if the staticSeoUrlContent
     *             had an ID already.
     */
    public function store(IdType $staticSeoUrlId, StaticSeoUrlContentInterface $staticSeoUrlContent)
    {
        $sqlData = [
            'static_seo_url_id' => $staticSeoUrlId->asInt(),
            'language_id'       => $staticSeoUrlContent->getLanguageId(),
            'title'             => $staticSeoUrlContent->getTitle(),
            'description'       => $staticSeoUrlContent->getDescription(),
            'keywords'          => $staticSeoUrlContent->getKeywords()
        ];
        
        $staticSeoUrlContentId = $staticSeoUrlContent->getId();
        if ($staticSeoUrlContentId !== 0) {
            $this->db->update($this->table, $sqlData, ['static_seo_url_content_id' => $staticSeoUrlContentId]);
        } else {
            $this->db->insert($this->table, $sqlData);
            $staticSeoUrlContentId = $this->db->insert_id();
        }
        
        return $staticSeoUrlContentId;
    }
}