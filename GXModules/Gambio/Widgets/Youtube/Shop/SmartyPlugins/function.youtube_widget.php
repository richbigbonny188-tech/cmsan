<?php
/*--------------------------------------------------------------
   function.youtube_widget.php 2023-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

use Gambio\CookieConsentPanel\Services\Purposes\Factories\PurposeReaderServiceFactory;

/**
 * @param $params
 * @param $smarty
 *
 * @return string
 */
function smarty_function_youtube_widget($params, &$smarty)
{
    $service = (new PurposeReaderServiceFactory)->service();
    [
        'id'                 => $id,
        'title'              => $title,
        'width'              => $width,
        'height'             => $height,
        'responsiveness'     => $responsiveness,
        'showPlayerControls' => $showPlayerControls,
    ] = $params;
    
    $showPlayerControls = $showPlayerControls === '1';
    
    $command = new YoutubeWidgetOutputCommand($service,
                                              $id,
                                              $width,
                                              $height,
                                              $title,
                                              $responsiveness,
                                              $showPlayerControls);
    
    return $command->execute();
}