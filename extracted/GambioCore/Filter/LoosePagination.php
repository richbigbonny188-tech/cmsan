<?php
/* --------------------------------------------------------------
   LoosePagination.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

use Webmozart\Assert\Assert;

/**
 * Class LoosePagination
 *
 * @package Gambio\Core\Filter
 */
class LoosePagination implements Pagination
{
    /**
     * @var int
     */
    protected $limit;
    
    /**
     * @var int
     */
    protected $offset;
    
    
    /**
     * Pagination constructor.
     *
     * @param int $limit
     * @param int $offset
     */
    protected function __construct(int $limit, int $offset)
    {
        $this->limit  = $limit;
        $this->offset = $offset;
    }
    
    
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return static
     */
    public static function createWithLimitAndOffset(
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ) {
        Assert::greaterThan($limit, 0, 'Limit needs to be greater than 0. Got: %s');
        Assert::greaterThanEq($offset, 0, 'Offset needs to be greater or equals 0. Got: %s');
        
        return new static($limit, $offset);
    }
    
    
    /**
     * @param int $perPage
     * @param int $page
     *
     * @return static
     */
    public static function createWithPageAndPerPage(
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE
    ) {
        Assert::greaterThanEq($page, 0, 'Page needs to be greater or equals 0. Got: %s');
        Assert::greaterThan($perPage, 0, 'Per page value needs to be greater than 0. Got: %s');
        
        return static::createWithLimitAndOffset($perPage, ($page - 1) * $perPage);
    }
    
    
    /**
     * @return int
     */
    public function limit(): int
    {
        return $this->limit;
    }
    
    
    /**
     * @return int
     */
    public function offset(): int
    {
        return $this->offset;
    }
}