<?php
/*
 * --------------------------------------------------------------
 *   OmnibusPolicyOverloadTrait.inc.php 2022-05-25
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

/**
 * Trait OmnibusPolicyOverloadTrait
 */
trait OmnibusPolicyOverloadTrait
{
    /**
     * @var GambioOmnibusPolicyReviews
     */
    private $reviewsService;


    /**
     * @return bool
     */
    private function moduleIsInstalled()
    {
        $moduleIsInModuleCenter = gm_get_conf('MODULE_CENTER_GAMBIOOMNIBUSPOLICY_INSTALLED');

        return $moduleIsInModuleCenter && $moduleIsInModuleCenter == 1;
    }


    /**
     * @return GambioOmnibusPolicyReviewsService
     */
    private function reviews()
    {
        if (!$this->reviewsService) {
            $this->reviewsService = MainFactory::create('GambioOmnibusPolicyReviewsService');
        }

        return $this->reviewsService;
    }

}