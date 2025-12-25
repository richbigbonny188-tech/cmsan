<?php
/* --------------------------------------------------------------
  ContentZoneNormalizeTrait.php 2019-10-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\ContentZone\Entities\Traits;

use stdClass;

/**
 * Trait ContentZoneNormalizeTrait
 */
trait ContentZoneNormalizeTrait
{
    /**
     * @param stdClass $object
     * @param string   $type
     *
     * @return stdClass
     */
    protected static function setType(stdClass $object, string $type): stdClass
    {
        if (!isset($object->type) && $type) {
            $object->type = $type;
        }
        
        return $object;
    }
}
