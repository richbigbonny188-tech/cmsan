<?php
/* --------------------------------------------------------------
   ProductsApiV2ControllerLegacy.php 2017-12-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @api             {post} /products Create Product
 * @apiVersion      2.1.0
 * @apiName         CreateProduct
 * @apiGroup        Products
 *
 * @apiDescription
 * Creates a new product record in the system. To see an example usage take a look at
 * `docs/REST/samples/product-service/create_product.php`
 *
 * @apiParamExample {json} Request-Body
 * {
 *   "isActive": false,
 *   "sortOrder": 0,
 *   "orderedCount": 1,
 *   "productModel": "ABC123",
 *   "ean": "",
 *   "price": 16.7983,
 *   "discountAllowed": 0,
 *   "taxClassId": 1,
 *   "quantity": 998,
 *   "weight": 0,
 *   "shippingCosts": 0,
 *   "shippingTimeId": 1,
 *   "productTypeId": 1,
 *   "manufacturerId": 0,
 *   "isFsk18": false,
 *   "isVpeActive": false,
 *   "vpeID": 0,
 *   "vpeValue": 0,
 *   "name": {
 *     "en": "test article",
 *     "de": "Testartikel"
 *   },
 *   "description": {
 *     "en": "[TAB:Page 1] Test Product Description (Page 1) [TAB: Page 2] Test Product Description (Page 2)",
 *     "de": "[TAB:Seite 1] Testartikel Beschreibung (Seite 1) [TAB:Seite 2] Testartikel Beschreibung (Seite 2)"
 *   },
 *   "shortDescription": {
 *     "en": "<p>Test product short description.</p>",
 *     "de": "<p>Testartikel Kurzbeschreibung</p>"
 *   },
 *   "keywords": {
 *     "en": "",
 *     "de": ""
 *   },
 *   "metaTitle": {
 *     "en": "",
 *     "de": ""
 *   },
 *   "metaDescription": {
 *     "en": "",
 *     "de": ""
 *   },
 *   "metaKeywords": {
 *     "en": "",
 *     "de": ""
 *   },
 *   "url": {
 *     "en": "",
 *     "de": ""
 *   },
 *   "urlKeywords": {
 *     "en": "test-article",
 *     "de": "Testartikel"
 *   },
 *   "checkoutInformation": {
 *     "en": "",
 *     "de": ""
 *   },
 *   "viewedCount": {
 *     "en": 0,
 *     "de": 32
 *   },
 *   "images": [
 *     {
 *       "filename": "artikelbild_1_1.jpg",
 *       "isPrimary": false,
 *       "isVisible": true,
 *       "imageAltText": {
 *         "en": "",
 *         "de": ""
 *       }
 *     },
 *     {
 *       "filename": "artikelbild_1_2.jpg",
 *       "isPrimary": false,
 *       "isVisible": true,
 *       "imageAltText": {
 *         "en": "",
 *         "de": ""
 *       }
 *     },
 *     {
 *       "filename": "artikelbild_1_3.jpg",
 *       "isPrimary": false,
 *       "isVisible": true,
 *       "imageAltText": {
 *         "en": "",
 *         "de": ""
 *       }
 *     }
 *   ],
 *   "settings": {
 *     "detailsTemplate": "standard.html",
 *     "optionsDetailsTemplate": "product_options_dropdown.html",
 *     "optionsListingTemplate": "product_options_dropdown.html",
 *     "showOnStartpage": false,
 *     "showQuantityInfo": true,
 *     "showWeight": false,
 *     "showPriceOffer": true,
 *     "showAddedDateTime": false,
 *     "priceStatus": 0,
 *     "minOrder": 1,
 *     "graduatedQuantity": 1,
 *     "onSitemap": true,
 *     "sitemapPriority": "0.5",
 *     "sitemapChangeFrequency": "daily",
 *     "propertiesDropdownMode": "dropdown_mode_1",
 *     "startpageSortOrder": 0,
 *     "showPropertiesPrice": true,
 *     "usePropertiesCombisQuantity": false,
 *     "usePropertiesCombisShippingTime": true,
 *     "usePropertiesCombisWeight": false
 *   },
 *   "addonValues": {
 *     "productsImageWidth": "0",
 *     "productsImageHeight": "0"
 *   }
 * }
 *
 * @apiParam {Boolean} isActive Whether the product is active.
 * @apiParam {Number} sortOrder The sort order of the product.
 * @apiParam {Number} orderedCount How many times the product was ordered.
 * @apiParam {String} productModel Product's Model.
 * @apiParam {String} ean European Article Number.
 * @apiParam {Number} price Product's Price as float value.
 * @apiParam {Number} discountAllowed Percentage of the allowed discount as float value.
 * @apiParam {Number} taxClassId The tax class ID.
 * @apiParam {Number} quantity Quantity in stock as float value.
 * @apiParam {Number} weight The weight of the product as float value.
 * @apiParam {Number} shippingCosts Additional shipping costs as float value.
 * @apiParam {Number} shippingTimeId Must match a record from the shipping time entries.
 * @apiParam {Number} productTypeId Must match a record from the product type entries.
 * @apiParam {Number} manufacturerId Must match the ID of the manufacturer record.
 * @apiParam {Boolean} isFsk18 Whether the product is FSK18.
 * @apiParam {Boolean} isVpeActive Whether VPE is active.
 * @apiParam {Number} vpeID The VPE ID of the product.
 * @apiParam {Number} vpeValue The VPE value of the product as float value.
 * @apiParam {Object} name Language specific object with the product's name.
 * @apiParam {Object} description Language specific object with the product's description.
 * @apiParam {Object} shortDescription Language specific object with the product's short description.
 * @apiParam {Object} keywords Language specific object with the product's keywords.
 * @apiParam {Object} metaTitle Language specific object with the product's meta title.
 * @apiParam {Object} metaDescription Language specific object with the product's meta description.
 * @apiParam {Object} metaKeywords Language specific object with the product's meta keywords.
 * @apiParam {Object} url Language specific object with the product's url.
 * @apiParam {Object} urlKeywords Language specific object with the product's url keywords.
 * @apiParam {Object} checkoutInformation Language specific object with the product's checkout information.
 * @apiParam {Object} viewedCount Language specific object with the product's viewed count.
 * @apiParam {Array} images Contains the product images information.
 * @apiParam {String} images.filename The product image file name (provide only the file name and not the whole
 *           path).
 * @apiParam {Boolean} images.isPrimary Whether the image is the primary one.
 * @apiParam {Boolean} images.isVisible Whether the image will be visible.
 * @apiParam {Object} images.imageAltText Language specific object with the image alternative text.
 * @apiParam {Object} settings Contains various product settings.
 * @apiParam {String} settings.detailsTemplate Filename of the details HTML template.
 * @apiParam {String} settings.optionsDetailsTemplate Filename of the options details HTML template.
 * @apiParam {String} settings.optionsListingTemplate Filename of the options listing HTML template.
 * @apiParam {Boolean} settings.showOnStartpage Whether to show the product on startpage.
 * @apiParam {Boolean} settings.showQuantityInfo Whether to show quantity information.
 * @apiParam {Boolean} settings.showWeight Whether to show the products weight.
 * @apiParam {Boolean} settings.showPriceOffer Whether to show price offer.
 * @apiParam {Boolean} settings.showAddedDateTime Whether to show the creation date-time of the product.
 * @apiParam {Number} settings.priceStatus Must match a record from the price status entries.
 * @apiParam {Number} settings.minOrder The minimum order of the product.
 * @apiParam {Number} settings.graduatedQuantity Product's graduated quantity.
 * @apiParam {Boolean} settings.onSitemap Whether to include the product in the sitemap.
 * @apiParam {String} settings.sitemapPriority The sitemap priority (provide a decimal value as a string).
 * @apiParam {String} settings.sitemapChangeFrequency Possible values can contain the `always`, `hourly`, `daily`,
 * `weekly`, `monthly`, `yearly`, `never`.
 * @apiParam {String} settings.propertiesDropdownMode Provide one of the following values: "" >>  Default - all
 * values are always selectable, `dropdown_mode_1` >> Any order, only possible values are selectable,
 * `dropdown_mode_2` >> Specified order, only possible values are selectable.
 * @apiParam {Number} settings.startpageSortOrder The sort order in the startpage.
 * @apiParam {Boolean} settings.showPropertiesPrice Whether to show properties price.
 * @apiParam {Boolean} settings.usePropertiesCombisQuantity Whether to use properties combis quantitity.
 * @apiParam {Boolean} settings.usePropertiesCombisShippingTime Whether to use properties combis shipping time.
 * @apiParam {Boolean} settings.usePropertiesCombisWeight  Whether to use properties combis weight.
 * @apiParam {Object} addonValues Contains some extra addon values.
 * @apiParam {String} addonValues.productsImageWidth The CSS product image width (might contain size metrics).
 * @apiParam {String} addonValues.productsImageHeight The CSS product image height (might contain size metrics).
 *
 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Product resource in the
 * response body.
 *
 * @apiError        400-BadRequest The body of the request was empty.
 *
 * @apiErrorExample Error-Response
 * HTTP/1.1 400 Bad Request
 * {
 *   "code": 400,
 *   "status": "error",
 *   "message": "The body of the request was empty."
 * }
 */