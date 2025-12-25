<?php

/* --------------------------------------------------------------
   SlideRepositoryWriter.inc.php 2016-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SlideRepositoryWriterInterface');

/**
 * Class SlideRepositoryWriter
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideRepositoryWriter implements SlideRepositoryWriterInterface
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
     * SlideRepositoryWriter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Inserts a slide to the database.
     *
     * @param IdType         $sliderId
     * @param SlideInterface $slide
     *
     * @return int ID of inserted slide or the given slide ID if the slide had an ID already.
     */
    public function store(IdType $sliderId, SlideInterface $slide)
    {
        $dataArray = [];
        
        $slideId = $slide->getId();
        if ($slideId !== 0) {
            $dataArray['slide_id'] = $slideId;
        }
        
        $dataArray['slider_id']   = $sliderId->asInt();
        $dataArray['language_id'] = $slide->getLanguageId();
        $dataArray['thumbnail']   = $slide->getThumbnail();
        $dataArray['title']       = $slide->getTitle();
        $dataArray['alt_text']    = $slide->getAltText();
        $dataArray['url']         = $slide->getUrl();
        $dataArray['url_target']  = $slide->getUrlTarget();
        $dataArray['sort_order']  = $slide->getSortOrder();
        
        $this->db->insert($this->table, $dataArray);
        
        if ($slideId === 0) {
            return $this->db->insert_id();
        } else {
            return $slideId;
        }
    }
    
    
    /**
     * Unset the thumbnail filename references in other slide thumbnail entry by the given filename.
     *
     * @param FilenameStringType $filename Slide image filename.
     *
     * @return SlideRepositoryWriterInterface Same instance for method chaining.
     */
    public function unsetSlideThumbnailReference(FilenameStringType $filename)
    {
        $this->db->update($this->table, ['thumbnail' => ''], ['thumbnail' => $filename->asString()]);
        
        return $this;
    }
}