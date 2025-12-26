<?php
/* --------------------------------------------------------------
   WithdrawalCreated.php 2021-05-14
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
 * Class WithdrawalCreated
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\Events
 * @codeCoverageIgnore
 */
class WithdrawalCreated
{
    /**
     * @var WithdrawalId
     */
    private $id;
    
    
    /**
     * WithdrawalCreated constructor.
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
     * @return WithdrawalCreated
     */
    public static function create(WithdrawalId $id): WithdrawalCreated
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