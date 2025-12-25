<?php
/* --------------------------------------------------------------
   GxAdapterTrait.php 2018-08-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Adapters;

/**
 * Trait GxAdapterTrait
 *
 * @package Gambio\AdminFeed\Adapters
 */
trait GxAdapterTrait
{
    private $gxAdapter;
    
    
    /**
     * @param GxAdapter $adapter
     */
    public function setGxAdapter(GxAdapter $adapter)
    {
        $this->gxAdapter = $adapter;
    }
    
    
    /**
     * @return GxAdapter
     */
    private function gxAdapter()
    {
        if ($this->gxAdapter === null) {
            $this->gxAdapter = new GxAdapter();
        }
        
        return $this->gxAdapter;
    }
}