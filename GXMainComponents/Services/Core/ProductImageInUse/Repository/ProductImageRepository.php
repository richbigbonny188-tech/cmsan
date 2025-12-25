<?php
/*--------------------------------------------------------------
   ProductImageRepository.php 2020-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class ProductImageRepository
 */
class ProductImageRepository implements ProductImageRepositoryInterface
{
    /**
     * @var ProductImageReaderInterface
     */
    protected $reader;
    
    /**
     * @var ProductImageBaseNameFactoryInterface
     */
    protected $factory;
    
    
    /**
     * ProductImageRepository constructor.
     *
     * @param ProductImageReaderInterface          $reader
     * @param ProductImageBaseNameFactoryInterface $factory
     */
    public function __construct(ProductImageReaderInterface $reader, ProductImageBaseNameFactoryInterface $factory)
    {
        $this->reader  = $reader;
        $this->factory = $factory;
    }
    
    
    public function imagesInUse(): array
    {
        $result = [];
        $dtos   = $this->reader->imagesInUse();
        
        if (count($dtos)) {
    
            foreach ($dtos as $dto) {
                
                $result[] = $this->createBaseName($dto->value());
            }
        }
        
        return $result;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createBaseName(string $path): ProductImageBaseName
    {
        return $this->factory->create($path);
    }
}