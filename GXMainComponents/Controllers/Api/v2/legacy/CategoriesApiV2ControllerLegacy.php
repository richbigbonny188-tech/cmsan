<?php
/* --------------------------------------------------------------
   CategoriesApiV2ControllerLegacy.php 2017-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @api             {get} /categories/:id Get Categories
 * @apiVersion      2.1.0
 * @apiName         GetCategory
 * @apiGroup        Categories
 *
 * @apiDescription
 * Get multiple or a single category records through a GET request. This method supports all the GET parameters
 * that are mentioned in the "Introduction" section of this documentation. To see an example usage take a look at
 * `docs/REST/samples/category-service/fetch_category.php`
 *
 * @apiExample {curl} Get All Categories
 *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/categories
 *
 * @apiExample {curl} Get Category With ID = 57
 *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/categories/57
 *
 * @apiExample {curl} Get Children of Category With ID = 23
 *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/categories/23/children
 *
 * @apiError        404-NotFound Category does not exist.
 *
 * @apiErrorExample Error-Response
 * HTTP/1.1 404 Not Found
 * {
 *   "code": 404,
 *   "status": "error",
 *   "message": "Category does not exist."
 * }
 */