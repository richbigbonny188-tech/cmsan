<?php
/* --------------------------------------------------------------
  JavaScriptErrorHandler.inc.php 2020-05-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class JavaScriptErrorHandler extends JavaScriptErrorHandler_parent
{
	function proceed()
	{
		if($this->is_js_error_logger_enabled())
		{
			$this->v_output_buffer['JavaScriptErrorHandler'] = "\t\t" . '<script type="text/javascript" src="gm/javascript/GMJavaScriptErrorHandler.js"></script>' . "\n";
			$this->v_output_buffer['JavaScriptErrorHandler'] .= "\t\t" . '<script type="text/javascript">' . "\n";
			$this->v_output_buffer['JavaScriptErrorHandler'] .= "\t\t\t" . 'window.onerror = handleJsError;' . "\n";
			$this->v_output_buffer['JavaScriptErrorHandler'] .= "\t\t" . '</script>' . "\n";
		}
		
		parent::proceed();
	}
    
    
    protected function is_js_error_logger_enabled(): bool
    {
        return isset($GLOBALS['coo_debugger']) && is_object($GLOBALS['coo_debugger'])
               && $GLOBALS['coo_debugger']->is_enabled('log_js_errors');
    }
}