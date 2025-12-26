<?php

/* --------------------------------------------------------------
   SliderRepositoryDeleter.inc.php 2016-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SliderRepositoryDeleterInterface');

/**
 * Class SliderRepositoryDeleter
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SliderRepositoryDeleter implements SliderRepositoryDeleterInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'sliders';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SliderRepositoryDeleter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Deletes a Slider by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $sliderId)
    {
        $this->db->delete($this->table, ['slider_id' => $sliderId->asInt()]);
        
        return $this;
    }
}