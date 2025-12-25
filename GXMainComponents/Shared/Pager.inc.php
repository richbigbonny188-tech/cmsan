<?php
/* --------------------------------------------------------------
   Pager.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Pager
 */
class Pager
{
    /**
     * @var int
     */
    protected $page;
    
    /**
     * @var int
     */
    protected $perPage;
    
    /**
     * @var int
     */
    protected $offset;
    
    
    /**
     * Pager constructor.
     *
     * @param IntType $page    Current page.
     * @param IntType $perPage Items per page.
     */
    public function __construct(IntType $page, IntType $perPage)
    {
        $this->page    = $page->asInt() < 1 ? 1 : $page->asInt();
        $this->perPage = $perPage->asInt();
        $this->offset  = $this->page <= 1 ? 0 : ($this->page - 1) * $this->perPage;
    }
    
    
    /**
     * Named constructor of pager.
     *
     * @param int $page    Current page.
     * @param int $perPage Items per page.
     *
     * @return Pager New instance.
     *
     * @throws InvalidArgumentException
     */
    public static function create($page, $perPage)
    {
        return MainFactory::create(static::class, new IntType($page), new IntType($perPage));
    }
    
    
    /**
     * Named constructor of pager.
     *
     * @param int $page    Current page.
     * @param int $perPage Items per page.
     *
     * @return Pager New instance.
     *
     * @throws InvalidArgumentException
     */
    public static function createCustom($offset, $limit)
    {
        $result          = MainFactory::create(static::class, new IntType(1), new IntType(1));
        $result->perPage = $limit;
        $result->offset  = $offset;
        
        return $result;
    }
    
    
    /**
     * Returns the current page.
     *
     * @return int
     */
    public function page()
    {
        return $this->page;
    }
    
    
    /**
     * Returns the items count per page.
     *
     * @return int
     */
    public function perPage()
    {
        return $this->perPage;
    }
    
    
    /**
     * Returns the offset.
     *
     * @return int
     */
    public function offset()
    {
        return $this->offset;
    }
}