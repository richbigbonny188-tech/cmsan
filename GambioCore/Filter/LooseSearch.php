<?php
/*--------------------------------------------------------------
   LooseSearch.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Filter;

/**
 * Class LooseSearch
 *
 * @package Gambio\Core\Filter
 */
class LooseSearch implements Search
{
    private string $keyword;
    
    
    /**
     * @param string $keyword
     */
    private function __construct(string $keyword)
    {
        $this->keyword = $keyword;
    }
    
    
    /**
     * @param string $keyword
     *
     * @return static
     */
    public static function create(string $keyword)
    {
        return new static($keyword);
    }
    
    
    /**
     * @inheritDoc
     */
    public function keyword(): string
    {
        return $this->keyword;
    }
}