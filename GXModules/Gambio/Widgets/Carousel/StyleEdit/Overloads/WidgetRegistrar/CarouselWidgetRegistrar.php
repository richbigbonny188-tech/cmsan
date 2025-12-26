<?php
/* --------------------------------------------------------------
  CarouselWidgetRegistrar.php 2023-06-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class CarouselWidgetRegistrar
 */
class CarouselWidgetRegistrar extends CarouselWidgetRegistrar_parent
{
    public function proceed(): void
    {
        parent::proceed();
        
        $this->addWidget(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'widget.json');
    }
}