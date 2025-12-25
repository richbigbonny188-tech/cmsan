<?php
/* --------------------------------------------------------------
  WidgetRepository.php 2019-07-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\Widget;

use Gambio\StyleEdit\Core\Components\Widget\Entities\WidgetConfiguration;
use Gambio\StyleEdit\Core\SingletonPrototype;
use ReflectionException;
use WidgetRegistrar;

/**
 * Class WidgetRepository
 */
class WidgetRepository
{
    /**
     * @var WidgetRegistrar
     */
    protected $registrar;
    
    /**
     * @var WidgetConfiguration[]
     */
    protected $widgets = [];
    
    
    /**
     * WidgetRepository constructor
     * @throws ReflectionException
     * @throws \Exception
     */
    public function __construct()
    {
        $this->registrar = SingletonPrototype::instance()->get(WidgetRegistrar::class);
        $this->buildWidgetList();
    }


    /**
     *
     * @throws ReflectionException
     * @throws \Exception
     */
    protected function buildWidgetList(): void
    {
        foreach ($this->registrar->getWidgetsJsonFiles() as $json) {
            
            $this->widgets[] = WidgetConfiguration::createFromJsonPath($json);
        }
    }
    
    
    /**
     * @return WidgetConfiguration[]
     */
    public function widgets(): array
    {
        return $this->widgets;
    }
    
}