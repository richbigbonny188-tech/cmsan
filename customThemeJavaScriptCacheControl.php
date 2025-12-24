<?php
/* --------------------------------------------------------------
   customThemeJavaScriptCacheControl 2023-05-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include_once 'includes/application_top.php';

if(isset($_SESSION['language_charset']))
{
	header('Content-Type: text/javascript; charset=' . $_SESSION['language_charset']);
}
else
{
	header('Content-Type: text/javascript; charset=utf-8');
}


$httpCaching = MainFactory::create_object('HTTPCaching');
$httpCaching->start_output_buffer();

// Set correct path of the theme
$publishedThemePath = StyleEditServiceFactory::service()->getPublishedThemePath();
$arguments = $publishedThemePath ?
    [ "{$publishedThemePath}/javascripts/system" ] :
    null;
$customThemeJavaScriptController = MainFactory::create_object(
    'CustomThemeJavaScriptController',
    $arguments
);
$script                          = $_GET['script'] ?? '';
$directory                       = $_GET['directory'] ?? '';

$customThemeJavaScriptController->includeScript($directory, $script);

$jsContent = $httpCaching->stop_output_buffer();

$httpCaching->send_header($jsContent, false, false, 'no-cache', time() + (60 * 60 * 24 * 31), '');
$httpCaching->check_cache($jsContent);

echo $jsContent;