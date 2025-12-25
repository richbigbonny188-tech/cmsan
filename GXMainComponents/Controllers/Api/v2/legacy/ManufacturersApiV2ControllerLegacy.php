<?php
/* --------------------------------------------------------------
   ManufacturersApiV2ControllerLegacy.php 2018-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @api             {post} /manufacturers Create Manufacturers
 * @apiVersion      2.5.0
 * @apiName         CreateManufacturers
 * @apiGroup        Manufacturers
 *
 * @apiDescription
 * This method enables the creation of a new Manufacturers into the system.
 *
 * @apiParamExample {json} Manufacturers entity
 * {
 *      "name": "Breitling",
 *      "image": "manufacturers/breitling-logo.png",
 *      "urls": {
 *          "EN": "https://breitling.com",
 *          "DE": "https://breitling.de"
 *      }
 * }
 *
 * @apiParam {String} name Name of manufacturer.
 * @apiParam {String} image Path to manufacturers image.
 * @apiParam {Object} urls Object with language code as key and the language specific url as value.
 *
 * @apiSuccess (201) Request-Body If successful, this method returns the complete manufacturers resource
 * in the response body.
 *
 * @apiSuccessExample {json} Success-Response
 *  {
 *      "id": 2,
 *      "name": "Breitling",
 *      "image": "manufacturers/breitling-logo.png",
 *      "dateAdded": {
 *          "date": "2017-09-29 10:37:27.000000",
 *          "timezone_type": 3,
 *          "timezone": "Europe/Berlin"
 *      },
 *      "lastModified": {
 *          "date": "2017-09-29 10:37:27.000000",
 *          "timezone_type": 3,
 *          "timezone": "Europe/Berlin"
 *      },
 *      "urls": {
 *          "EN": "https://breitling.com",
 *          "DE": "https://breitling.de"
 *      }
 *  }
 *
 * @apiError        400-BadRequest The body of the request was empty.
 * @apiErrorExample Error-Response
 * HTTP/1.1 400 Bad Request
 * {
 *   "code": 400,
 *   "status": "error",
 *   "message": "Manufacturers data were not provided."
 * }
 */

/**
 * @api             {patch} /manufacturers/:id Updates Manufacturers
 * @apiVersion      2.5.0
 * @apiName         UpdateManufacturers
 * @apiGroup        Manufacturers
 *
 * @apiDescription
 * Use this method if you want to update an existing manufacturers record.
 *
 * @apiParamExample {json} Manufacturers entity
 * {
 *      "name": "Breitling",
 *      "image": "manufacturers/breitling-logo.png",
 *      "urls": {
 *          "EN": "https://breitling.com",
 *          "DE": "https://breitling.de"
 *      }
 * }
 *
 * @apiParam {String} name Name of manufacturer.
 * @apiParam {String} image Path to manufacturers image.
 * @apiParam {Object} urls Object with language code as key and the language specific url as value.
 *
 * @apiSuccess (200) Request-Body If successful, this method returns the complete manufacturers resource
 * in the response body.
 *
 * @apiSuccessExample {json} Success-Response
 *  {
 *      "id": 2,
 *      "name": "Breitling",
 *      "image": "manufacturers/breitling-logo.png",
 *      "dateAdded": {
 *          "date": "2017-09-29 10:37:27.000000",
 *          "timezone_type": 3,
 *          "timezone": "Europe/Berlin"
 *      },
 *      "lastModified": {
 *          "date": "2017-09-29 10:37:27.000000",
 *          "timezone_type": 3,
 *          "timezone": "Europe/Berlin"
 *      },
 *      "urls": {
 *          "EN": "https://breitling.com",
 *          "DE": "https://breitling.de"
 *      }
 *  }
 *
 * @apiError        400-BadRequest Manufacturers data were not provided or manufacturers record ID was not provided
 *                  or is invalid.
 *
 * @apiErrorExample Error-Response (Empty request body)
 * HTTP/1.1 400 Bad Request
 * {
 *   "code": 400,
 *   "status": "error",
 *   "message": "Manufacturers data were not provided."
 * }
 *
 * @apiErrorExample Error-Response (Missing or invalid ID)
 * HTTP/1.1 400 Bad Request
 * {
 *   "code": 400,
 *   "status": "error",
 *   "message": "Manufacturers record ID was not provided or is invalid."
 * }
 *
 * @apiError        404-NotFoundRequest Manufacturers data were not provided or manufacturers record ID was not
 *                  provided or is invalid.
 *
 * @apiErrorExample Error-Response (Entity not found)
 * HTTP/1.1 400 Bad Request
 * {
 *   "code": 404,
 *   "status": "error",
 *   "message": "Manufacturer entity was not found with provided id [ID]"
 * }
 */