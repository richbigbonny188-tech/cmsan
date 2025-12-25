<?php

/* --------------------------------------------------------------
   SliderRepositoryWriter.inc.php 2016-08-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SliderRepositoryWriterInterface');

/**
 * Class SliderRepositoryWriter
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SliderRepositoryWriter implements SliderRepositoryWriterInterface
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
     * SliderRepositoryWriter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Inserts a slider to the database.
     *
     * @param SliderInterface $slider
     *
     * @return int ID of inserted slider or the given slider ID if the slider had an ID already.
     */
    public function store(SliderInterface $slider)
    {
        $dataArray = [];
        
        $sliderId = $slider->getId();
        if ($sliderId !== 0) {
            $dataArray['slider_id'] = $sliderId;
        }
        
        $dataArray['name']       = $slider->getName();
        $dataArray['speed']      = $slider->getSpeed();
        $dataArray['start_page'] = (int)$slider->showOnStartPage();
        
        $this->db->insert($this->table, $dataArray);
        
        if ($sliderId === 0) {
            return $this->db->insert_id();
        } else {
            return $sliderId;
        }
    }
    
    
    /**
     * Set the Slider for the start page.
     *
     * @param IdType $sliderId
     *
     * @return SliderRepositoryWriterInterface Same instance for method chaining.
     */
    public function setStartPageSlider(IdType $sliderId)
    {
        $this->db->update($this->table, ['start_page' => 0]);
        $this->db->update($this->table, ['start_page' => 1], ['slider_id' => $sliderId->asInt()]);
        
        return $this;
    }
}