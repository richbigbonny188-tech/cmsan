<?php
/* --------------------------------------------------------------
	hub_phrases.lang.inc.php 2020-02-21
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

if(empty($GLOBALS['db_link']))
{
	$t_language_text_section_content_array = [];

	return;
}

$sql = '
    SELECT `value` FROM `gx_configurations` WHERE `key` = "gm_configuration/INSTALLED_VERSION"
';

$result = xtc_db_query($sql);

$config = xtc_db_fetch_array($result);

$installed_version = $config['value'];

if (!$installed_version) {
    $t_language_text_section_content_array = [];

    return;
}

// use xtc_db_query for compatibility with installer and updater
$sql = '
    SELECT `value`
    FROM `gx_configurations`
    WHERE `key` = "gm_configuration/GAMBIO_HUB_PHRASES_URL"
';

$result = xtc_db_query($sql);

if(xtc_db_num_rows($result) > 0)
{
    $row         = xtc_db_fetch_array($result);
    $phrases_url = $row['value'];
    $curlTimeout = 10;

    $sql = '
        SELECT `value`
        FROM `gx_configurations`
        WHERE `key` = "gm_configuration/GAMBIO_HUB_CURL_TIMEOUT"
    ';

    $result = xtc_db_query($sql);

	if(xtc_db_num_rows($result) > 0)
	{
		$row         = xtc_db_fetch_array($result);
		$curlTimeout = (int)$row['value'];
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $phrases_url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $curlTimeout);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$jsonResponse = json_decode(curl_exec($ch), true);

	$t_language_text_section_content_array = $jsonResponse['de'];

	if(!is_array($t_language_text_section_content_array))
	{
		$t_language_text_section_content_array = [];
	}
}
else
{
	$t_language_text_section_content_array = [];
}
