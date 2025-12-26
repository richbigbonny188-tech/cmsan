<?php
/* --------------------------------------------------------------
   MetaData.php 2021-03-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace GProtector;

class MetaData
{
    /**
     * @var int|null
     */
    private $modificationDateUnixTime;
    /**
     * @var int
     */
    private $lastUpdateCheckUnixTime;


    /**
     * MetaData constructor.
     *
     * @param int|null $modificationDateUnixTime
     * @param int $lastUpdateCheckUnixTime
     */
    public function __construct($modificationDateUnixTime, $lastUpdateCheckUnixTime)
    {
        $this->modificationDateUnixTime = $modificationDateUnixTime;
        $this->lastUpdateCheckUnixTime  = $lastUpdateCheckUnixTime;
    }


    /**
     * @return int|null
     */
    public function modificationDateUnixTime()
    {
        return $this->modificationDateUnixTime;
    }


    /**
     * @return int
     */
    public function lastUpdateCheckUnixTime()
    {
        return $this->lastUpdateCheckUnixTime;
    }
}