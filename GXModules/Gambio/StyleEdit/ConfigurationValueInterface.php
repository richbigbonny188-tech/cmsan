<?php
/*--------------------------------------------------------------------------------------------------
    ConfigurationValueInterface.php 2019-08-30
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit;

use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;

/**
 * Interface ConfigurationValueInterface
 * @package Gambio\StyleEdit
 */
interface ConfigurationValueInterface extends SingletonStrategyInterface
{
    /**
     * @return mixed
     */
    public function value();
    
}