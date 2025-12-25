<?php
/* --------------------------------------------------------------
   ParcelServiceWriteService.inc.php 2018-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ParcelServiceWriteService implements \ParcelServiceWriteServiceInterface
{
    /**
     * @var \ParcelServiceRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ParcelServiceReadService constructor.
     *
     * @param \ParcelServiceRepositoryInterface $repository
     */
    public function __construct(\ParcelServiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Saves a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function save(\ParcelServiceInterface $parcelService)
    {
        return $this->repository->save($parcelService);
    }
    
    
    /**
     * Updates a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function update(\ParcelServiceInterface $parcelService)
    {
        return $this->repository->update($parcelService);
    }
    
    
    /**
     * Deletes a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     */
    public function delete(\ParcelServiceInterface $parcelService)
    {
        $this->repository->delete($parcelService);
    }
}