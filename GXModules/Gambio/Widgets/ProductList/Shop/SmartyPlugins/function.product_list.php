<?php
/* --------------------------------------------------------------
  function.product.php 2023-11-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * @param                          $params
 * @param Smarty_Internal_Template $template
 *
 * @return string
 * @throws Exception
 */
function smarty_function_product_list($params, &$template)
{
    $callback = function ($value) {
        return str_replace("'", '', $value);
    };
    $maxProducts = min((int)($params['maxProducts'] ?? null), ProductListWidgetCommandConfiguration::MAX_PRODUCTS_LIMIT);

    $params = array_map($callback, $params);
    $constructorParams = [
        'categoryId'  => (int)$params['categoryId'] ?? null,
        'listType'    => $params['listType'] ?? null,
        'random'      => ($params['random'] ?? null) === 'true',
        'itemsPerRowXs'      => $params['itemsPerRowXs'] ?? null,
        'itemsPerRowSm'      => $params['itemsPerRowSm'] ?? null,
        'itemsPerRowMd'      => $params['itemsPerRowMd'] ?? null,
        'itemsPerRowLg'      => $params['itemsPerRowLg'] ?? null,
        'maxProducts' => (int)($maxProducts),
        'presentation' => $params['presentation'] ?? null,
        'id'           => $params['id'] ?? null,
        'class'        => $params['class'] ?? null,
    ];
    $constructorParams['languageId'] = (int)$params['languageId'];
    $constructorParams['languageCode'] = MainFactory::create(LanguageCode::class, new StringType((string)$params['languageCode']));

    $configurationFactory = MainFactory::create(ProductListWidgetConfigurationFactory::class);

    $construct = [
        $configurationFactory->createCommandConfigurationFromArray($constructorParams),
        explode(',', $params['products'])
    ];

    $result = MainFactory::create(ProductListWidgetOutputCommand::class, ...$construct)->execute() ?? '';

    if (!$result) {
        $result = '';
    }

    return $result;
}
