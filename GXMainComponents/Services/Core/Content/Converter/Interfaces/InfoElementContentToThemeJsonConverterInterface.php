<?php
/* --------------------------------------------------------------
  InfoElementContentToThemeJsonConverterInterface.php 2019-12-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface InfoElementContentToThemeJsonConverterInterface
 */
interface InfoElementContentToThemeJsonConverterInterface
{
    /**
     * @param InfoElementContent $content
     *
     * @return stdClass
     */
    public function convert(InfoElementContent $content): stdClass;
}