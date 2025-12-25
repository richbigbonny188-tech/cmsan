<?php
/* --------------------------------------------------------------
   gm_get_session_name_postfix.inc.php 2019-09-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Returns a postfix value for the session cookie name.
 *
 * The postfix is specific to the installation of the shop system. Thus, multiple installations can coexist on the
 * same domain without interfering with each other’s sessions. This could otherwise happen if installations exist e.g.
 * on https://example.com/ and https://subshop.example.com/ because the cookie path ("/") is identical and a session
 * cookie for the installation on example.com would also get sent to the subdomain subshop.example.com.
 * Another way around this problem would be to limit session cookies to the exact host name which in turn might cause
 * problems for installations which allow access via example.com and www.example.com.
 *
 * cf. https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies#Scope_of_cookies
 *
 * @return string
 */
function gm_get_session_name_postfix()
{
    $identificationCriteria = [GM_HTTP_SERVER, DIR_WS_CATALOG, DIR_FS_CATALOG];
    $postfixFileContent = implode('|', $identificationCriteria);
    if (!is_writable(DIR_FS_CATALOG . 'media')) {
        throw new RuntimeException('media directory MUST be writable!');
    }
    $sessionNamePostfixFiles = glob(DIR_FS_CATALOG . 'media/session_name_postfix_*', GLOB_NOSORT);
    if (empty($sessionNamePostfixFiles)) {
        $sessionNamePostfix = (string)substr(hash('sha256', mt_rand()), 0 , 16);
        file_put_contents(DIR_FS_CATALOG . 'media/session_name_postfix_' . $sessionNamePostfix, $postfixFileContent);
    } else { // count($sessionNamePostfixFiles) >= 1
        if (count($sessionNamePostfixFiles) > 1) {
            foreach (array_slice($sessionNamePostfixFiles, 1) as $extraFile) {
                unlink($extraFile);
            }
        }
        $sessionNamePostfixFile = $sessionNamePostfixFiles[0];
        $existingPostfixFileContent = file_get_contents($sessionNamePostfixFile);
        if ($existingPostfixFileContent === $postfixFileContent) {
            $sessionNamePostfix = (string)str_replace('session_name_postfix_', '', basename($sessionNamePostfixFile));
        } else {
            unlink($sessionNamePostfixFile);
            $sessionNamePostfix = (string)substr(hash('sha256', mt_rand()), 0 , 16);
            file_put_contents(DIR_FS_CATALOG . 'media/session_name_postfix_' . $sessionNamePostfix, $postfixFileContent);
        }
    }
    if (empty($sessionNamePostfix)) {
        throw new RuntimeException('could not determine session name postfix');
    }
    return $sessionNamePostfix;
}
