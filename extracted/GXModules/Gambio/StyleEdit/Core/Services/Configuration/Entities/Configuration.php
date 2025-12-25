<?php
/* --------------------------------------------------------------
  Configuration.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Entities;

use AbstractCollection;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\OptionInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration extends AbstractCollection implements ConfigurationInterface
{
    /**
     * Get valid type.
     *
     * This method must be implemented in the child-collection classes.
     *
     * @return string
     */
    protected function _getValidType(): string
    {
        return OptionInterface::class;
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getArray();
    }
}