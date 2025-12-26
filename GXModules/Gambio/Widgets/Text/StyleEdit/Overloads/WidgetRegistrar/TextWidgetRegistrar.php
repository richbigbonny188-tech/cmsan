<?php
/* --------------------------------------------------------------
 TextWidgetRegistrar.php 2019-08-01
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class TextWidgetRegistrar
 */
class TextWidgetRegistrar extends TextWidgetRegistrar_parent
{
    public function proceed(): void
    {
        parent::proceed();
        
        $this->addWidget(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'widget.json');
    }
}
