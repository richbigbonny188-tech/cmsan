<?php

/* --------------------------------------------------------------
   DebugBar.inc.php 2018-02-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \DebugBar\DataCollector\ConfigCollector;
use \DebugBar\DataCollector\MessagesCollector;
use \DebugBar\StandardDebugBar;

/**
 * Class DebugBar
 *
 * Extends the standard DebugBar to provide shortcuts to the collections
 *
 * @category   System
 * @package    Extensions
 * @subpackage DebugBar
 */
class DebugBar extends StandardDebugBar
{
    /**
     * DebugBar constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        // Prepare the config collector.
        $serverInfo = MainFactory::create('ServerInfoMaster');
        
        $misc = [
            'Shop Constants' => get_defined_constants(true)['user'],
            'Server Info'    => $serverInfo->get_server_info()
        ];
        
        $this->addCollector(new ConfigCollector($misc, 'misc'));
        
        // Prepare the welcome collector.
        $this->addCollector(new MessagesCollector('help'));
        
        $help = [
            '=========================',
            'Gambio GX - PHP Debug Bar',
            '=========================',
            '',
            'The Debug Bar helps developers display execution information while working with the shop platform.',
            '',
            'Use the shorthand commands for common tasks:',
            '',
            ' - DebugBar::addMessage(\'Display Variable Values: \' . $value);',
            ' - DebugBar::addException(new Exception(\'Displays exception information.\'));',
            ' - DebugBar::startMeasure(\'custom-measure-name\');',
            ' - DebugBar::stopMeasure(\'custom-measure-name\');',
            ' - DebugBar::measure(\'custom-measure-name\', function() { sleep(2); });',
            '',
            'Get the original Debug Bar instance to access more helper methods:',
            '',
            ' - $debugBar = StaticGXCoreLoader::getDebugBar();',
            '',
            'Disable the Debug Bar for the current page by adding a "hide_debug_bar" GET parameter:',
            '',
            'https://example.org/admin/start.php?hide_debug_bar',
            '',
            'More information can be found at http://phpdebugbar.com',
        ];
        
        foreach ($help as $row) {
            $this['help']->addMessage($row);
        }
    }
    
    
    /**
     * This is a shorthand for adding messages to the message collection.
     *
     * @param string $message  The message to add to the debug bar messages collection
     * @param string $label    The debug level, choose between info, error and warning
     * @param bool   $isString Whether the message is a string or something else
     */
    public static function addMessage($message, $label = 'info', $isString = true)
    {
        StaticGXCoreLoader::getDebugBar()['messages']->addMessage($message, $label, $isString);
    }
    
    
    /**
     * This is a shorthand for adding exceptions to the exception collection.
     *
     * @param Exception $exception The exception to add to the exception collection.
     */
    public static function addException(Exception $exception)
    {
        StaticGXCoreLoader::getDebugBar()['exceptions']->addException($exception);
    }
    
    
    /**
     * This is a shorthand for starting a time measurement.
     *
     * @param string      $name      Internal name, used to stop the measure.
     * @param string|null $label     Public name.
     * @param string|null $collector The source of the collector.
     */
    public static function startMeasure($name, $label = null, $collector = null)
    {
        StaticGXCoreLoader::getDebugBar()['time']->startMeasure($name, $label, $collector);
    }
    
    
    /**
     * This is a shorthand for stopping a time measurement.
     *
     * @param string $name Internal name, used to stop the measure.
     */
    public static function stopMeasure($name)
    {
        StaticGXCoreLoader::getDebugBar()['time']->stopMeasure($name);
    }
    
    
    /**
     * This is a shorthand for measuring the execution of a closure.
     *
     * @param string  $label   Public name.
     * @param Closure $closure Closure to be executed.
     */
    public static function measure($label, Closure $closure)
    {
        StaticGXCoreLoader::getDebugBar()['time']->measure($label, $closure);
    }
}