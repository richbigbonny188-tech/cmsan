<?php
/*--------------------------------------------------------------
   ProductImageInUseService.php 2020-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class ProductImageInUseService
 */
class ProductImageInUseService implements ProductImageInUseServiceInterface
{
    /**
     * @var ProductImageRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var ProductImageBaseName[]
     */
    protected $imagesInUse;
    
    
    /**
     * ProductImageService constructor.
     *
     * @param ProductImageRepositoryInterface $repository
     */
    public function __construct(ProductImageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function imageIsInUse(string $fileName): bool
    {
        $usageCounter = 0;
        $images       = $this->imagesInUse();
        $file         = $this->repository->createBaseName($fileName);
        
        if (count($images)) {
            
            foreach ($images as $image) {
                
                if ($file->equals($image)) {
    
                    $usageCounter++;
                }
            }
        }
        
        return $usageCounter > 1;
    }
    
    
    /**
     * @return array|ProductImageBaseName[]
     */
    protected function imagesInUse(): array
    {
        if ($this->imagesInUse === null) {
    
            $this->imagesInUse = $this->repository->imagesInUse();
        }
        
        return $this->imagesInUse;
    }
}