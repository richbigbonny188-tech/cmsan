<?php
/* --------------------------------------------------------------
   AgreementRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementRepository
 *
 * @category   System
 * @package    Agreement
 * @subpackage Repositories
 */
class AgreementRepository implements AgreementRepositoryInterface
{
    
    /**
     * @var \AgreementWriterInterface
     */
    protected $writer;
    
    /**
     * @var \AgreementDeleterInterface
     */
    protected $deleter;
    
    
    /**
     * AgreementRepository constructor.
     *
     * @param \AgreementWriterInterface  $writer
     * @param \AgreementDeleterInterface $deleter
     */
    public function __construct(AgreementWriterInterface $writer, AgreementDeleterInterface $deleter)
    {
        $this->writer  = $writer;
        $this->deleter = $deleter;
    }
    
    
    /**
     * Stores the provided agreement.
     *
     * @param \AgreementInterface $agreement
     *
     * @return $this|\AgreementWriteServiceInterface Same instance for chained method calls.
     */
    public function store(AgreementInterface $agreement)
    {
        $agreement->getId() === 0 ? $this->writer->store($agreement) : $this->writer->update($agreement);
        
        return $this;
    }
    
    
    /**
     * Deletes the provided agreement.
     *
     * @param \AgreementInterface $agreement
     *
     * @return $this|\AgreementWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(AgreementInterface $agreement)
    {
        $this->deleter->delete($agreement);
        
        return $this;
    }
}