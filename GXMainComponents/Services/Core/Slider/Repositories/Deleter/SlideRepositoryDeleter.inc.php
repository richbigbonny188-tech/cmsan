<?php

/* --------------------------------------------------------------
   SlideRepositoryDeleter.inc.php 2016-08-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SlideRepositoryDeleterInterface');

/**
 * Class SlideRepositoryDeleter
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideRepositoryDeleter implements SlideRepositoryDeleterInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'slides';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SlideRepositoryDeleter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Deletes a Slide by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $slideId)
    {
        $this->db->delete($this->table, ['slide_id' => $slideId->asInt()]);
        
        return $this;
    }
}