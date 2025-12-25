<?php
/* --------------------------------------------------------------
   LoadTemplateData.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\TemplateEngine\Engines\Smarty\Plugins;

use BadMethodCallException;
use Closure;
use Gambio\Core\Application\Application;
use Smarty_Internal_Template;

/**
 * Class LoadTemplateData
 *
 * @package Gambio\Core\TemplateEngine\Engines\Smarty\Plugins
 */
class LoadTemplateData
{
    /**
     * @var Application
     */
    private $application;
    
    /**
     * @var array<string|int, array>
     */
    private $extraData = [];
    
    /**
     * @var array<string|int, array>
     */
    private $fetchedData = [];
    
    
    /**
     * LoadTemplateData constructor.
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }
    
    
    /**
     * Load language text smarty callback function.
     *
     * @return Closure
     */
    public function callback(): callable
    {
        return function (array $params, Smarty_Internal_Template $smarty) {
            if (array_key_exists('class', $params) === false) {
                throw new BadMethodCallException('Method call must provide "class" parameter.');
            }
            
            $class = $params['class'];
            $key   = $params['key'] ?? str_repace('\\', '_', $class);
            
            $data = [];
            if (array_key_exists($class, $this->fetchedData)) {
                $data = $this->fetchedData[$class];
            } elseif ($this->application->has($class)) {
                $this->fetchedData[$class] = $this->application->get($class)();
                $data                      = $this->fetchedData[$class];
            }
            
            $this->extraData[$key] = $data;
            $smarty->assign('extra', $this->extraData);
        };
    }
}