<?php
/* --------------------------------------------------------------------
   OrderMailService.inc.php 2023-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------------*/

MainFactory::load_class('OrderMailServiceInterface');

class OrderMailService implements OrderMailServiceInterface
{
    /**
     * Stores the html mail body for given order ID.
     *
     * @param int    $orderId
     * @param string $mailBody
     *
     * @return bool
     */
    public function writeHtml(int $orderId, string $mailBody): bool
    {
        $zip = new ZipArchive();
        $zipFilename = $this->getFilePath($orderId);
        if (!$zip->open($zipFilename, ZipArchive::CREATE)) {
            throw new RuntimeException("html order mail for order ID $orderId could not be stored");
        }
        $zip->addFromString("order_mail-{$orderId}.html", $mailBody);
        $zip->close();
        
        return true;
    }
    
    
    /**
     * Stores the txt mail body for given order ID.
     *
     * @param int    $orderId
     * @param string $mailBody
     *
     * @return bool
     */
    public function writeTxt(int $orderId, string $mailBody): bool
    {
        $zip = new ZipArchive();
        $zipFilename = $this->getFilePath($orderId);
        if (!$zip->open($zipFilename, ZipArchive::CREATE)) {
            throw new RuntimeException("txt order mail for order ID $orderId could not be stored");
        }
        $zip->addFromString("order_mail-{$orderId}.txt", $mailBody);
        $zip->close();
        
        return true;
    }
    
    
    /**
     * Returns the html mail body for given order ID.
     *
     * @param int $orderId
     *
     * @return string
     */
    public function readHtml(int $orderId): string
    {
        return $this->getZipFileContent($orderId, true);
    }
    
    
    /**
     * Returns the txt the mail body for given order ID.
     *
     * @param int $orderId
     *
     * @return string
     */
    public function readTxt(int $orderId): string
    {
        return $this->getZipFileContent($orderId, false);
    }
    
    
    /**
     * @param int  $orderId
     *
     * @return string
     */
    protected function getFilePath(int $orderId): string
    {
        $savePath = DIR_FS_CATALOG . 'export/order_mails';
        
        if (!is_writable($savePath)) {
            throw new RuntimeException(DIR_FS_CATALOG . 'export/order_mails is not writable');
        }
        
        // store 10000 files at max in one directory
        $savePath .= '/' . (int)floor($orderId / 10000);
        
        if (!file_exists($savePath)) {
            @mkdir($savePath);
            @chmod($savePath, 0777);
        }
        
        if (!file_exists($savePath)) {
            throw new RuntimeException("$savePath directory cannot be created");
        }
        
        if (!is_writable($savePath)) {
            throw new RuntimeException("$savePath is not writable");
        }
        
        $hash = gm_get_conf('ORDER_MAIL_HASH');
        
        if (!$hash) {
            $hash = 'abc';
            gm_set_conf('ORDER_MAIL_HASH', $hash);
            //throw new UnexpectedValueException('ORDER_MAIL_HASH is missing');
        }
        
        return "{$savePath}/{$orderId}-{$hash}.zip";
    }
    
    
    /**
     * @param int  $orderId
     * @param bool $html
     *
     * @return string
     */
    protected function getZipFileContent(int $orderId, bool $html): string
    {
        $contents = '';
        $fp = fopen('zip://' . $this->getFilePath($orderId, $html) . "#order_mail-{$orderId}." . ($html ? 'html' : 'txt'), 'r');
        if (!$fp) {
            throw new RuntimeException("order mail for order ID $orderId does not exist");
        }
        while (!feof($fp)) {
            $contents .= fread($fp, 2);
        }
        fclose($fp);
        
        return $contents;
    }
}