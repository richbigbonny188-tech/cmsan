<?php
/* --------------------------------------------------------------
   ImageProcessingCronjobTask.inc.php 2018-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ImageProcessingCronjobTask
 */
class ImageProcessingCronjobTask extends AbstractCronjobTask
{
    /**
     * Image Processing Timeout
     */
    const MAX_EXECUTION_TIME_IN_SECONDS = 20;
    
    
    /**
     * Returns the cronjob callback.
     *
     * @param float $cronjobStartAsMicrotime
     *
     * @return \Closure
     */
    public function getCallback($cronjobStartAsMicrotime)
    {
        return function () use ($cronjobStartAsMicrotime) {
            
            $this->logger->lastRun();
            
            /** @var ImageProcessingService $service */
            $service           = $this->dependencies->getDependencies()['ImageProcessingService'];
            $recreateAllImages = $this->dependencies->getDependencies()['recreateAllImages'];
            $files             = $this->getImageFiles();
            $unprocessedFiles  = [];
            $imageNumber       = 1;
            
            // check for progress
            if (file_exists(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_progress')) {
                $imageNumber = (int)file_get_contents(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_progress');
                $imageNumber = $imageNumber > 0 ? $imageNumber : 1;
            }
            
            if (file_exists(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_last_number')) {
                $lastNumber = (int)file_get_contents(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_last_number');
                
                // skip number, if last processed image number equals last number to avoid never ending cronjob
                $imageNumber = $imageNumber === $lastNumber ? $imageNumber + 1 : $imageNumber;
                
                if (isset($files[$imageNumber - 1])) {
                    $this->logger->logError([basename($files[$imageNumber - 1])]);
                }
            }
            
            for ($filesCount = count($files); $imageNumber <= $filesCount; $imageNumber++) {
                // store current image number
                file_put_contents(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_last_number', $imageNumber);
                
                $file = MainFactory::create('ExistingFile', new \NonEmptyStringType($files[$imageNumber - 1]));
                
                $success = $recreateAllImages ? $service->recreateAllImages($file) : $service->createMissingImages($file);
                
                if (!$success) {
                    // remember unprocessable filenames
                    $unprocessedFiles[] = basename($file->getFilePath());
                }
                
                // store progress
                file_put_contents(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_progress', $imageNumber + 1);
                
                // pause image processing if timeout is reached
                if (microtime(true) - $cronjobStartAsMicrotime > self::MAX_EXECUTION_TIME_IN_SECONDS) {
                    break;
                }
            }
            
            // reset progress if processing is finished
            if ($imageNumber > $filesCount
                && file_exists(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_progress')) {
                unlink(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_progress');
                ImageProcessingService::removeTrigger();
            }
            
            // log unprocessed filenames
            if (count($unprocessedFiles)) {
                $this->logger->logError($unprocessedFiles);
            }
            
            // log summary
            $this->logger->log([
                                   'total image count' => $filesCount,
                                   'last image number' => $imageNumber > $filesCount ? $filesCount : $imageNumber
                               ]);
            
            $this->logger->lastSuccess();
            
            return true;
        };
        
        return $this->closure;
    }
    
    
    /**
     * Returns the cronjob schedule.
     *
     * @return string
     */
    public function getSchedule()
    {
        // start next cronjob call immediately, if image processing is not finished yet
        if (file_exists(DIR_FS_CATALOG . 'cache/cronjobs/image_processing_progress')
            || ImageProcessingService::isTriggerSet()) {
            return '* * * * *';
        }
        
        return parent::getSchedule();
    }
    
    
    /**
     * Returns an array of image file paths.
     *
     * @return array
     */
    protected function getImageFiles()
    {
        $files = [];
        
        if ($dir = opendir(DIR_FS_CATALOG . 'images/product_images/original_images')) {
            while ($file = readdir($dir)) {
                if (is_file(DIR_FS_CATALOG . 'images/product_images/original_images/' . $file)
                    && (strripos($file, '.jpg') !== false
                        || strripos($file, '.jpeg') !== false
                        || strripos($file, '.gif') !== false
                        || strripos($file, '.png') !== false)) {
                    $files[] = DIR_FS_CATALOG . 'images/product_images/original_images/' . $file;
                }
            }
            closedir($dir);
            
            sort($files);
        }
        
        return $files;
    }
    
}