<?php
/* --------------------------------------------------------------
   theme_settings.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$themeSettingsArray = [
    'THEME_PRESENTATION_VERSION' => 4.0
];

$themeSettings = MainFactory::create_object('DefaultThemeSettings');
$themeSettings->setThemeSettingsArray($themeSettingsArray);
$themeSettingsArray = $themeSettings->getThemeSettingsArray();
