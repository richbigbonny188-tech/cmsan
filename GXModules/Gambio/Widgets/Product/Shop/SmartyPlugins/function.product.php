<?php
/* --------------------------------------------------------------
  function.product_list.php 2019-09-13
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
 * @return mixed|string
 * @throws Exception
 */
function smarty_function_product($params, Smarty_Internal_Template $template)
{
    $callback = function ($value) {
        return str_replace("'", '', $value);
    };
    $params = array_map($callback, $params);

    $productId = isset($params['id']) ? new IdType($params['id']) : null;

    $configurationFactory = MainFactory::create(ProductWidgetConfigurationFactory::class);
    $commandConfiguration = $configurationFactory->createCommandConfigurationFromArray([
        'id'           => $params['elementId'] ?? '',
        'class'        => $params['class'] ?? '',
        'languageId'   => $params['languageId'] ?? $_SESSION['language_id'],
        'languageCode' => $params['languageCode'] ?? $_SESSION['language_code']
    ]);

    return MainFactory::create(ProductWidgetOutputCommand::class, $productId, $commandConfiguration)->execute();
}

