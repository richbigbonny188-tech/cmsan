<?php
/*--------------------------------------------------------------------------------------------------
    cookie_purpose_is_enabled.php 2020-3-20
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


/**
 * Checks whether the purpose is enabled by the customer (Shop front-end)
 *
 * @param int $purposeId
 *
 * @return bool
 */
function cookie_purpose_is_enabled(int $purposeId): bool
{
    return CookieConsentManager::getInstance()->purposeStatus((int)$purposeId);
}

