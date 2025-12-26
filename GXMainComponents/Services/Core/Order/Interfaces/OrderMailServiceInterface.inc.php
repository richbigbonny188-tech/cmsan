<?php
/* --------------------------------------------------------------
   OrderObjectServiceInterface.php 2023-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

interface OrderMailServiceInterface
{
    /**
     * Stores the html mail body for given order ID.
     *
     * @param int    $orderId
     * @param string $mailBody
     *
     * @return bool
     */
    public function writeHtml(int $orderId, string $mailBody): bool;
    
    
    /**
     * Stores the txt mail body for given order ID.
     *
     * @param int    $orderId
     * @param string $mailBody
     *
     * @return bool
     */
    public function writeTxt(int $orderId, string $mailBody): bool;
    
    
    /**
     * Returns the html mail body for given order ID.
     *
     * @param int $orderId
     *
     * @return string
     */
    public function readHtml(int $orderId): string;
    
    
    /**
     * Returns the txt the mail body for given order ID.
     *
     * @param int $orderId
     *
     * @return string
     */
    public function readTxt(int $orderId): string;
}