<?php
/* --------------------------------------------------------------
   SqlPagination.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class SqlPagination
 *
 * @package Gambio\Core\Filter
 */
class SqlPagination extends LoosePagination
{
    /**
     * @param QueryBuilder $query
     */
    public function applyToQuery(QueryBuilder $query): void
    {
        if ($this->limit !== null) {
            $query->setMaxResults($this->limit);
        }
        
        if ($this->offset !== null) {
            $query->setFirstResult($this->offset);
        }
    }
}