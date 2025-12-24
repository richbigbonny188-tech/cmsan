<?php
/* --------------------------------------------------------------
   StockLogger.inc.php 2019-02-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class StockLogger
{
    /** @var string */
    protected $logFileName;
    
    /** @var string */
    protected $baseLogFileName;
    
    /** @var int */
    protected $maxLogFileSize;
    
    /** @var \ProductReadService */
    protected $productReadService;
    
    const DEFAULT_MAX_LOG_FILE_SIZE = 1048576; // one mebibyte ought to be enough for anybody
    
    
    public function __construct()
    {
        $this->setLogFileName(MainFactory::create('NonEmptyStringType', 'stocklog'));
        $this->setMaxLogFileSize(self::DEFAULT_MAX_LOG_FILE_SIZE);
        /** @var \ProductReadService $productReadService */
        $this->productReadService = StaticGXCoreLoader::getService('ProductRead');
    }
    
    
    public function setLogFileName(NonEmptyStringType $fileName)
    {
        $this->baseLogFileName = basename($fileName->asString());
        $this->logFileName     = sprintf('%s-%s.log', $this->baseLogFileName, LogControl::get_secure_token());
    }
    
    
    public function addLogEntry(
        IdType $productId,
        DecimalType $newStock,
        NonEmptyStringType $eventDescription,
        StringType $productVariation = null
    ) {
        $product = $this->productReadService->getProductById($productId);
        
        $defaultLanguage = MainFactory::create('LanguageCode', new StringType(DEFAULT_LANGUAGE));
        
        $logData  = [
            (new DateTime())->format('Y-m-d\TH:i:s.uP'),
            $productId->asInt(),
            $product->getProductModel(),
            $product->getName($defaultLanguage),
            $newStock->asDecimal(),
            $eventDescription->asString(),
            $productVariation === null ? '' : $productVariation->asString(),
        ];
        $logEntry = implode(',', $logData) . "\n";
        
        $this->initLogFile();
        file_put_contents(DIR_FS_CATALOG . 'logfiles/' . $this->logFileName, $logEntry, FILE_APPEND);
    }
    
    
    protected function initLogFile()
    {
        $logFilePathName = DIR_FS_CATALOG . 'logfiles/' . $this->logFileName;
        
        if (file_exists($logFilePathName) && filesize($logFilePathName) > $this->getMaxLogFileSize()) {
            $archiveFilePathName = sprintf('%s/logfiles/%s-%s-%s.log',
                                           DIR_FS_CATALOG,
                                           $this->baseLogFileName,
                                           (new DateTime())->format('YmdHis'),
                                           LogControl::get_secure_token());
            rename($logFilePathName, $archiveFilePathName);
        }
        
        if (!file_exists($logFilePathName)) {
            $headerData = [
                'Datum-Uhrzeit',
                'ProductID',
                'Art.-Nr.',
                'Artikelname',
                'neuer Bestand',
                'Ereignis',
                'Artikelvariante',
            ];
            $headerLine = implode(',', $headerData) . "\n";
            file_put_contents($logFilePathName, $headerLine);
        }
    }
    
    
    /**
     * @return int
     */
    public function getMaxLogFileSize()
    {
        return $this->maxLogFileSize;
    }
    
    
    /**
     * @param int $maxLogFileSize
     */
    public function setMaxLogFileSize($maxLogFileSize)
    {
        $this->maxLogFileSize = (int)$maxLogFileSize;
    }
}

