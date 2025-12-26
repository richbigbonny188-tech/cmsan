<?php
/* --------------------------------------------------------------
  PublishedThemeValidationServiceFactoryInterface.php 2019-12-12
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface PublishedThemeValidationServiceFactoryInterface
 */
interface PublishedThemeValidationServiceFactoryInterface
{
    /**
     * @return PublishedThemeValidationServiceInterface
     */
    public function service(): PublishedThemeValidationServiceInterface;
}