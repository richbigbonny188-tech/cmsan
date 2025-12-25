<?php
/* --------------------------------------------------------------
   error.php 2022-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

// Determine browser language
$languageCode = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$errorPageDir  = __DIR__ . '/public/error_pages';
if (!file_exists($errorPageDir . '/500-' . $languageCode . '.html')) {
    $languageCode = 'de';
}

// Setup HTML code
$htmlFile = $errorPageDir . '/500-' . $languageCode . '.html';
$htmlCode = '<p style="text-align: center; font-size: 26px;">Leider ist ein unerwarteter Fehler aufgetreten.</p>'
            . '<p style="text-align: center;">Fehler-Code: ##ERROR_CODE##</p>';
if (file_exists($htmlFile)) {
    $htmlCode = file_get_contents($htmlFile);
}
$errorCode = trim(filter_var($_GET['code'] ?? '', FILTER_SANITIZE_STRING));
$errorCode = ($errorCode !== '') ? $errorCode : 'Kein Fehlercode vorhanden';
$htmlCode  = str_replace('##ERROR_CODE##', $errorCode, $htmlCode);

// Show error page
header('HTTP/1.1 500 Internal Server Error', true, 500);
header('Cache-Control: no-cache');

echo $htmlCode;