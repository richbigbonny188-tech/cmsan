<?php
/* --------------------------------------------------------------
   DateRange.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DateRange
 */
class DateRange
{
    /**
     * @var \DateTime
     */
    protected $begin;
    
    /**
     * @var \DateTime
     */
    protected $end;
    
    
    /**
     * DateRange constructor.
     *
     * @param \DateTime $begin Begin of date range.
     * @param \DateTime $end   End of date range.
     */
    public function __construct(DateTime $begin, DateTime $end)
    {
        $this->begin = $begin;
        $this->end   = $end;
    }
    
    
    /**
     * Named constructor of date range.
     *
     * @param \DateTime $begin Begin of date range.
     * @param \DateTime $end   End of date range.
     *
     * @return \DateRange New instance.
     */
    public static function create(DateTime $begin, DateTime $end)
    {
        return MainFactory::create(static::class, $begin, $end);
    }
    
    
    /**
     * Returns the begin of the date range.
     *
     * @return \DateTime Begin of date range.
     */
    public function begin()
    {
        return $this->begin;
    }
    
    
    /**
     * Returns the end of the date range.
     *
     * @return \DateTime End of date range.
     */
    public function end()
    {
        return $this->end;
    }
}