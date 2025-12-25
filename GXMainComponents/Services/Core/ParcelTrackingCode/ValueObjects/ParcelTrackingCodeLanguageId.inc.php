<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeLanguageId.inc.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeLanguageId
 */
class ParcelTrackingCodeLanguageId
{
    /**
     * @var int
     */
    protected $id;
    
    
    /**
     * TrackingCodesLanguageId constructor.
     * Private to enforce usage of named constructor.
     *
     * @param int $id Language id of tracking code.
     */
    protected function __construct($id)
    {
        new IdType($id);
        $this->id = $id;
    }
    
    
    /**
     * Named constructor of tracking code language id.
     *
     * @param int $id Language id of tracking code.
     *
     * @return ParcelTrackingCodeLanguageId New instance.
     */
    public static function create($id)
    {
        return new static($id);
    }
    
    
    /**
     * Returns the language id of the tracking code.
     *
     * @return int $id Language id of tracking code.
     */
    public function is()
    {
        return $this->id;
    }
}