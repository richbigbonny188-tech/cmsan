<?php
/* --------------------------------------------------------------
   WithdrawalDeleted.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model\Events;

use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;

/**
 * Class WithdrawalDeleted
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\Events
 * @codeCoverageIgnore
 */
class WithdrawalDeleted
{
    /**
     * @var WithdrawalId
     */
    private $id;
    
    
    /**
     * WithdrawalDeleted constructor.
     *
     * @param WithdrawalId $id
     */
    private function __construct(WithdrawalId $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param WithdrawalId $id
     *
     * @return WithdrawalDeleted
     */
    public static function create(WithdrawalId $id): WithdrawalDeleted
    {
        return new self($id);
    }
    
    
    /**
     * @return WithdrawalId
     */
    public function withdrawalId(): WithdrawalId
    {
        return $this->id;
    }
}