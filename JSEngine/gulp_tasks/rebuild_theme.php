<?php
/* --------------------------------------------------------------
   rebuild_theme.php 2018-11-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

if(!array_key_exists(1, $argv))
{
	echo 'First argument for create_theme.php script must be the theme name.' . PHP_EOL;
	exit(1);
}

$themeName = $argv[1];

if(!is_dir(__DIR__ . '/../../src/themes/' . $themeName))
{
	echo 'Theme with name "' . $themeName . '" was not found in path "src/themes/". Please ensure that the theme exists'
	     . PHP_EOL;
	exit(1);
}

require_once __DIR__ . '/../../composer_components/autoload.php';
require_once __DIR__ . '/../../src/GXEngine/Shared/Types/StringType.inc.php';
require_once __DIR__ . '/../../src/GXEngine/Shared/Types/NonEmptyStringType.inc.php';
require_once __DIR__ . '/../../src/GXEngine/Shared/FileSystem/PathType.inc.php';
require_once __DIR__ . '/../../src/GXEngine/Shared/FileSystem/ExistingDirectory.inc.php';
require_once __DIR__ . '/../../src/GXEngine/Shared/AbstractCollection.inc.php';
require_once __DIR__ . '/../../src/GXEngine/Shared/FileSystem/ExistingFileCollection.inc.php';
require_once __DIR__ . '/../../src/GXEngine/Shared/KeyValueCollection.inc.php';
require_once __DIR__ . '/../../src/GXEngine/Shared/EditableKeyValueCollection.inc.php';

class MainFactory
{
	public static function create($className, ...$args)
	{
		return new $className(...$args);
	}
	
	public static function load_class($className)
    {
        return true;
    }
}

function getRecursiveDirectoryIterator($path)
{
	return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
	                                     RecursiveIteratorIterator::CHILD_FIRST);
}

$path = __DIR__ . '/../../src/GXMainComponents/Services/System/Theme';

$iterator = getRecursiveDirectoryIterator($path);
$files    = [];

foreach($iterator as $file)
{
	/** @var \SplFileInfo $file */
	if($file->isFile())
	{
		$files[$file->getFilename()] = $file->getPathname();
	}
}

spl_autoload_register(function ($class) use ($files) {
	$filename = $class . '.inc.php';
	
	if(array_key_exists($filename, $files))
	{
		require_once $files[$filename];
	}
});

$service = ThemeServiceFactory::createThemeService(new ExistingDirectory(__DIR__ . '/../../src'));

$themeId = ThemeId::create($themeName);

$source      = ThemeDirectoryRoot::create(new ExistingDirectory(__DIR__ . '/../../src/themes'));
$destination = ThemeDirectoryRoot::create(new ExistingDirectory(__DIR__ . '/../../src/public/theme'));
$settings    = ThemeSettings::create($source, $destination);

$service->buildTemporaryTheme($themeId, $settings);

echo 'The theme was successfully created in "/public/theme" directory.';
