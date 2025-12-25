<?php
/*--------------------------------------------------------------------------------------------------
    index.php 2019-08-15
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

$includePath = get_include_path();
set_include_path(dirname(__DIR__, 4) . '/');
require_once 'GXMainComponents/Application.inc.php';
require_once 'GXMainComponents/StyleEditApplication.php';
set_include_path($includePath);

$application = new \Gambio\GX\StyleEditApplication();
$application->run();

if (!isset($_SESSION['StyleEdit4Authentication'])) {
    $application->redirectToFrontEnd();
}

$helper = MainFactory::create(StyleEdit4PermissionHelper::class);

if (!$helper->checkPermissions()) {
    
    $languageTextManager                    = LanguageTextManager::get_instance('StyleEdit');
    $directoriesWithInsufficientPermissions = $helper->directoriesWithInsufficientPermissions();
    
    $message = sprintf($languageTextManager->get_text('INSUFFICIENT_PERMISSIONS'), current($directoriesWithInsufficientPermissions));
    
    $directoriesWithInsufficientPermissions = array_splice($directoriesWithInsufficientPermissions, 1);
    
    if (count($directoriesWithInsufficientPermissions)) {
        
        $message .= '<br /><br />' . implode('<br />', $directoriesWithInsufficientPermissions);
    }
    
    (new messageStack)->add_session(StyleEdit4PermissionHelper::class, $message, 'error');
    $application->redirectTemplateSettings();
}

$jwtToken = $_SESSION['StyleEdit4Authentication'];

$html = file_get_contents(__DIR__ . '/index.html');

// adding favicon
$favicon = "favicon.ico";
$coo_logo_manager = MainFactory::create_object('GMLogoManager', ['gm_logo_favicon']);
if ($coo_logo_manager->logo_use == '1') {
    $favicon = $coo_logo_manager->logo_path . $coo_logo_manager->logo_file;
}

// adding JWT token to the head of the file
$jwtScriptTag = '<script type="text/javascript">window.jwtToken = "' . $jwtToken . '";</script>';

$html = str_replace('<head>', '<head><link rel="icon" href="' . $favicon .'" type="image/x-icon"/>' . $jwtScriptTag, $html);

echo $html;