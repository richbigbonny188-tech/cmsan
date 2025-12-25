<?php
/* --------------------------------------------------------------
   CronjobsApiV2Controller.inc.php 2018-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class CronjobsApiV2Controller
 *
 * Provides a gateway to cronjob specific tasks.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class CronjobsApiV2Controller extends HttpApiV2Controller
{
    /**
     * @throws \HttpApiV2Exception
     * @api        {post} /cronjobs/ Do cronjob specific tasks
     * @apiVersion 2.7.0
     * @apiName    SetImageProcessingTrigger
     * @apiGroup   Cronjobs
     *
     * @apiDescription
     * Set trigger for ImageProcessing cronjob. This method will always return success.
     *
     * @apiExample {curl} Set image processing trigger
     *             curl -X POST --user admin@example.org:12345 https://example.org/api.php/v2/cronjobs/image_processing
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "code": 200,
     *   "status": "success",
     *   "action": "setImageProcessingTrigger"
     * }
     *
     */
    public function post()
    {
        // Check if task was provided.
        if (!isset($this->uri[1]) || !is_string($this->uri[1])) {
            throw new HttpApiV2Exception('Task name was not provided in the resource URL.', 400);
        }
        
        if (isset($this->uri[1]) && $this->uri[1] === 'image_processing') {
            ImageProcessingService::setTrigger();
            
            $response = [
                'code'   => 200,
                'status' => 'success',
                'action' => 'setImageProcessingTrigger'
            ];
            
            $this->_writeResponse($response);
        }
    }
}