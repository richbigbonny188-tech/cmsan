<?php
/* --------------------------------------------------------------
  function.content_zone.php 2019-07-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * @param                          $params
 * @param Smarty_Internal_Template $template
 *
 * @return string
 */
function smarty_function_content_zone($params, Smarty_Internal_Template $template)
{
    return (string)MainFactory::create(ContentZoneLoader::class, $params, $template);
}