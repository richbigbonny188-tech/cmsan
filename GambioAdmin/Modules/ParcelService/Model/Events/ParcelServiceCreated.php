<?php
/* --------------------------------------------------------------
   ParcelServiceCreated.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\Events;

use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;

/**
 * Class ParcelServiceCreated
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\Events
 * @codeCoverageIgnore
 */
class ParcelServiceCreated
{
    /**
     * @var ParcelServiceId
     */
    private $id;
    
    
    /**
     * ParcelServiceCreated constructor.
     *
     * @param ParcelServiceId $id
     */
    private function __construct(ParcelServiceId $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param ParcelServiceId $id
     *
     * @return ParcelServiceCreated
     */
    public static function create(ParcelServiceId $id): ParcelServiceCreated
    {
        return new self($id);
    }
    
    
    /**
     * @return ParcelServiceId
     */
    public function parcelServiceId(): ParcelServiceId
    {
        return $this->id;
    }
}