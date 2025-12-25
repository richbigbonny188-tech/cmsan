<?php

/* --------------------------------------------------------------
   StaticSeoUrlContentRepositoryReader.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StaticSeoUrlContentRepositoryReader
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Repositories
 */
class StaticSeoUrlContentRepositoryReader implements StaticSeoUrlContentRepositoryReaderInterface
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
     * StaticSeoUrlContentRepositoryReader constructor.
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns a StaticSeoUrlContentCollection for the given StaticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlContentCollection All staticSeoUrlContents found by the staticSeoUrlId ID as a
     *                                       StaticSeoUrlContentCollection.
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getByStaticSeoUrlId(IdType $staticSeoUrlId)
    {
        $staticSeoUrlContentArray = [];
        
        $result = $this->db->from($this->table)->where(['static_seo_url_id' => $staticSeoUrlId->asInt()])->get();
        
        foreach ($result->result_array() as $row) {
            $staticSeoUrlContent = MainFactory::create('StaticSeoUrlContent');
            $this->_setDbValues($staticSeoUrlContent, $row);
            
            $staticSeoUrlContentArray[] = $staticSeoUrlContent;
        }
        
        return MainFactory::create('StaticSeoUrlContentCollection', $staticSeoUrlContentArray);
    }
    
    
    /**
     * Returns a StaticSeoUrlContent instance by the given staticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlContentId
     *
     * @return StaticSeoUrlContentInterface
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getById(IdType $staticSeoUrlContentId)
    {
        $staticSeoUrlContentData = $this->db->get_where($this->table,
                                                        ['static_seo_url_content_id' => $staticSeoUrlContentId->asInt()])
            ->row_array();
        
        if ($staticSeoUrlContentData === null) {
            throw new UnexpectedValueException('The requested staticSeoUrlContent was not found in the database (ID:'
                                               . $staticSeoUrlContentId->asInt() . ')');
        }
        
        $staticSeoUrlContent = MainFactory::create('StaticSeoUrlContent');
        $this->_setDbValues($staticSeoUrlContent, $staticSeoUrlContentData);
        
        return $staticSeoUrlContent;
    }
    
    
    /**
     * Assign the staticSeoUrlContent values via the setters.
     *
     * @param StaticSeoUrlContent $staticSeoUrlContent StaticSeoUrlContent object.
     * @param array               $row                 Fetched DB row.
     *
     * @throws InvalidArgumentException If $row contains invalid values.
     */
    protected function _setDbValues(StaticSeoUrlContent $staticSeoUrlContent, array $row)
    {
        $staticSeoUrlContent->setId(new IdType($row['static_seo_url_content_id']))
            ->setLanguageId(new IdType($row['language_id']))
            ->setTitle(new StringType((string)$row['title']))
            ->setDescription(new StringType((string)$row['description']))
            ->setKeywords(new StringType((string)$row['keywords']));
    }
}