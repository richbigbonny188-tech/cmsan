<?php
/*--------------------------------------------------------------------------------------------------
    function.content_manager_alias.php 2019-12-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

require_once('function.content_manager.php');

/**
 * Helper function that generates the query
 * and executes it on the database
 *
 * @param       {integer}       $group          The content_group id of the desired db entry
 * @param       {integer}       $language       The language id of the desired db entry
 *
 * @return      array|bool|mixed                Returns the first result as array
 */
function _executeContentManagerAliasQuery($alias, $language)
{
    $groupCheck = '';
    if (GROUP_CHECK === 'true') {
        $groupCheck = ' AND group_ids LIKE "%c_' . xtc_db_input($_SESSION['customers_status']['customers_status_id'])
                      . '_group%"';
    }
    
    $queryString = 'SELECT '.TABLE_CONTENT_MANAGER.'.*
					FROM ' . TABLE_CONTENT_MANAGER . '
					INNER JOIN  content_manager_aliases
					ON          content_manager_aliases.`content_group` = ' . TABLE_CONTENT_MANAGER . '.`content_group`
					WHERE
						`content_alias` = "' . xtc_db_input(xtc_db_prepare_input($alias)) . '"
						AND	`languages_id` = ' . (int)$language . '
						AND `content_status` = 1' . $groupCheck;
    
    $query  = xtc_db_query($queryString);
    $result = [];
    
    if (xtc_db_num_rows($query)) {
        $result = xtc_db_fetch_array($query);
    }
    
    return $result;
}

/**
 * Helper function that calculates the
 * fallback language id if possible
 *
 * @param       {integer}       $source     The fallback language id
 * @param       {integer}       $language   The desired language id
 *
 * @return      integer|null                Returns the fallback id if there is one, otherwise null
 */
function _getContentManagerAliasFallbackId($source, $language)
{
    $fallback = (gettype($source) === 'integer') ? $source : $GLOBALS['coo_lang_file_master']->getDefaultLanguageId();
    $fallback = ((int)$fallback !== (int)$language) ? $fallback : null;
    
    return $fallback;
}

/**
 * Smarty plugin that gets content manager
 * elements from the database
 *
 * @param   {object}        $params     The passed parameters
 * @param   {object}        $smarty     Smarty object
 *
 * @return  string                      Returns the collected string from the db
 */
function smarty_function_content_manager_alias($params, &$smarty)
{
    $alias    = $params['alias'] ?? null;
    $get      = $params['get'] ?? null;
    $language = $params['lang'] ?? $_SESSION['languages_id'];
    $fallback = isset($params['fallback']) ? _getContentManagerAliasFallbackId($params['fallback'], $language) : null;
    $output   = $params['out'] ?? null;
    $result   = _executeContentManagerAliasQuery($alias, $language);
    
    if ($get === 'title') {
        return (count($result)) ? $result['content_title'] : '';
    } elseif ($get === 'heading') {
        return (count($result)) ? $result['content_heading'] : '';
    }
    
    if (empty($result) && $fallback !== null) {
        $result = _executeContentManagerAliasQuery($alias, $fallback);
    }
    
    if ($output !== null) {
        $smarty->assign($output, $result);
    } elseif (!empty($result['content_file'])) {
        $alias === 199 ? DIR_FS_CATALOG . 'media/content/' . $result['content_file'] : include DIR_FS_CATALOG . 'media/content/' . $result['content_file'];
    } else {
        
        $contentGroupId = preg_replace('#[\D]+#', '', $alias);
        return (count($result)) ? $result['content_text'] : smarty_function_content_manager(['group' => $contentGroupId], $smarty);
    }
}