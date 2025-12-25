<?php
/* --------------------------------------------------------------
   ParcelServiceDescriptionsUpdated.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\Events;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceDescriptions;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;

/**
 * Class ParcelServiceDescriptionsUpdated
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\Events
 * @codeCoverageIgnore
 */
class ParcelServiceDescriptionsUpdated
{
    /**
     * @var ParcelServiceId
     */
    private $id;
    
    /**
     * @var ParcelServiceDescriptions
     */
    private $descriptions;
    
    
    /**
     * ParcelServiceDescriptionsUpdated constructor.
     *
     * @param ParcelServiceId           $id
     * @param ParcelServiceDescriptions $description
     */
    private function __construct(ParcelServiceId $id, ParcelServiceDescriptions $description)
    {
        $this->id           = $id;
        $this->descriptions = $description;
    }
    
    
    /**
     * @param ParcelServiceId           $id
     * @param ParcelServiceDescriptions $description
     *
     * @return ParcelServiceDescriptionsUpdated
     */
    public static function create(
        ParcelServiceId $id,
        ParcelServiceDescriptions $description
    ): ParcelServiceDescriptionsUpdated {
        return new self($id, $description);
    }
    
    
    /**
     * @return ParcelServiceId
     */
    public function parcelServiceId(): ParcelServiceId
    {
        return $this->id;
    }
    
    
    /**
     * @return ParcelServiceDescriptions
     */
    public function descriptions(): ParcelServiceDescriptions
    {
        return $this->descriptions;
    }
}