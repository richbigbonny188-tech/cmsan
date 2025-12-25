<?php
/* --------------------------------------------------------------
   EuViesValidator.php 2024-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\VatValidation\App\Validators;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Gambio\Core\Logging\LoggerBuilder;
use Psr\Log\LoggerInterface;
use SoapClient;
use SoapFault;

/**
 * Class EuViesValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
class EuViesValidator
{
    protected const CACHE_TABLENAME = 'vatid_live_check_cache';
    protected const CACHE_TTL       = 60 * 60;
    
    protected const RATE_LIMIT_TABLENAME = 'vatid_live_check_triggered';
    protected const RATE_LIMIT_PERIOD    = 60;
    protected const RATE_LIMIT_AMOUNT    = 3;
    
    private LoggerInterface $logger;
    
    protected Connection $db;
    
    
    /**
     * @param LoggerBuilder $loggerFactory
     * @param Connection    $connection
     */
    public function __construct(LoggerBuilder $loggerFactory, Connection $connection)
    {
        $this->logger = $loggerFactory->changeNamespace('vat-validation')->omitRequestData()->build();
        $this->db     = $connection;
    }
    
    
    /**
     * @param string $vatId
     *
     * @return bool
     */
    public function validateVatId(string $vatId): bool
    {
        $cached         = $this->getCachedValidityAndError($vatId);
        $cachedValidity = $cached !== null ? $cached['valid'] : null;
        $cachedReason   = $cached !== null ? $cached['error'] : null;
        
        if ($cachedValidity === null) {
            $ip = $_SERVER['REMOTE_ADDR'];
            if ($this->isUsageAvailable($ip) && $this->cacheUsage($ip)) {
                $this->cleanUpUsages($ip);
                try {
                    $client  = new SoapClient("https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
                    $result  = $client->checkVat([
                                                     'countryCode' => substr($vatId, 0, 2),
                                                     'vatNumber'   => substr($vatId, 2),
                                                 ]);
                    $isValid = isset($result->valid) && $result->valid === true;
                    $this->cacheValidityAndReason($vatId, $isValid);
                    
                    return $isValid;
                } catch (SoapFault $exception) {
                    $reason = $exception->getMessage();
                    $this->logWarningValidationFailed($vatId, $reason);
                    $this->cacheValidityAndReason($vatId, false, $reason);
                    
                    return false;
                }
            } else {
                // rate limit exceeded
                return false;
            }
        } else {
            if ($cachedValidity === false) {
                if ($cachedReason !== null) {
                    $this->logWarningValidationFailed($vatId, $cachedReason);
                }
                
                return false;
            }
            
            return true;
        }
    }
    
    
    /**
     * @param string $vatId
     * @param string $reason
     *
     * @return void
     */
    protected function logWarningValidationFailed(string $vatId, string $reason): void
    {
        $this->logger->warning('Soap client for EU Vies service failed.',
                               ['vatId' => $vatId, 'reason' => $reason]);
    }
    
    
    /**
     * @param string $vatId
     *
     * @return null|array["valid" => bool, "error" => ?string]
     */
    protected function getCachedValidityAndError(string $vatId): ?array
    {
        try {
            $res = $this->db->executeQuery('
                SELECT c.valid, c.error
                FROM `' . self::CACHE_TABLENAME . '` c
                WHERE c.vatid = :vatid
                AND c.updated + :ttl > NOW()
            ',
                                           [
                                               'vatid' => $vatId,
                                               'ttl'   => self::CACHE_TTL,
                                           ]);
        } catch (\Exception $e) {
            $this->logger->error('Could not read vatid live check cache',
                                 $this->getContextFromThrowable($e));
            
            return null;
        }
        
        $output = $res->fetchAssociative();
        if ($output === false) {
            return null;
        }
        
        return [
            'valid' => boolval($output['valid']),
            'error' => $output['error'],
        ];
    }
    
    
    /**
     * Creates a context array from any throwable.
     * Serializes the throwable to an array.
     *
     * @param \Throwable $throwable
     *
     * @return array
     */
    private function getContextFromThrowable(\Throwable $throwable): array
    {
        return [
            'message' => $throwable->getMessage(),
            'code'    => $throwable->getCode(),
            'file'    => $throwable->getFile(),
            'line'    => $throwable->getLine(),
            'trace'   => $throwable->getTrace(),
        ];
    }
    
    
    /**
     * Write vat-id-live-check response to cache.
     *
     * @param string      $vatId
     * @param bool        $valid
     * @param string|null $reason
     *
     * @return bool
     */
    protected function cacheValidityAndReason(string $vatId, bool $valid, ?string $reason = null): bool
    {
        $data = [
            'vatid' => $vatId,
            'valid' => $valid,
            'error' => $reason,
        ];
        try {
            $this->db->executeStatement('
                    REPLACE INTO `' . self::CACHE_TABLENAME . '`
                    (`vatid`, `valid`, `error`)
                    VALUES  (:vatid, :valid, :error);
                ',
                                        $data);
            
            return true;
        } catch (DBALException $e) {
            $this->logger->error('Could not cache vatid check result',
                                 array_merge(['errorMessage' => $e->getMessage()], $data));
            
            return false;
        }
    }
    
    
    /**
     * Cache one usage of the vat-id-check.
     *
     * @param string $ip
     *
     * @return bool
     */
    protected function cacheUsage(string $ip): bool
    {
        $qb = $this->db->createQueryBuilder();
        try {
            $affectedRows = $qb->insert(self::RATE_LIMIT_TABLENAME)
                ->values(['ip' => ':ip'])
                ->setParameter('ip', $ip)
                ->executeStatement();
            
            return $affectedRows === 1;
        } catch (DBALException $e) {
            $this->logger->warning('Could not cache a VAT-ID-Live-Check usage.',
                                   array_merge([
                                                   'ip' => $ip,
                                               ],
                                               $this->getContextFromThrowable($e)));
            
            return false;
        }
    }
    
    
    /**
     * @param string $ip
     *
     * @return bool
     */
    protected function isUsageAvailable(string $ip): bool
    {
        $qb = $this->db->createQueryBuilder();
        try {
            $res    = $qb->select('COUNT(t.id) as c')
                ->from(self::RATE_LIMIT_TABLENAME, 't')
                ->where($qb->expr()
                            ->and($qb->expr()
                                      ->eq('t.ip',
                                           ':ip'),
                                  $qb->expr()
                                      ->gte('t.updated',
                                            'FROM_UNIXTIME(:period_start)')))
                ->setParameters([
                                    'ip'           => $ip,
                                    'period_start' => time() - self::RATE_LIMIT_PERIOD,
                                ])
                ->executeQuery();
            $usedUp = intval($res->fetchOne());
            if ($usedUp === false || $usedUp < self::RATE_LIMIT_AMOUNT) {
                return true;
            }
        } catch (DBALException $e) {
            $this->logger->warning('Could not check for available usages of VAT-ID-Live-Check.',
                                   array_merge([
                                                   'ip' => $ip,
                                               ],
                                               $this->getContextFromThrowable($e)));
            
            return false;
        }
        
        return false;
    }
    
    
    /**
     * Cleanup previous usages as to not clog the table unnecessarily.
     *
     * @param string $ip
     *
     * @return bool
     */
    protected function cleanUpUsages(string $ip): bool
    {
        $qb = $this->db->createQueryBuilder();
        try {
            $qb->delete(self::RATE_LIMIT_TABLENAME)
                ->where($qb->expr()->and($qb->expr()->eq('ip', ':ip'),
                                         $qb->expr()->lt('updated',
                                                         'FROM_UNIXTIME(:period_start)')))
                ->setParameters([
                                    'ip'           => $ip,
                                    'period_start' => time() - self::RATE_LIMIT_PERIOD,
                                ])
                ->executeStatement();
            
            return true;
        } catch (DBALException $e) {
            $this->logger->warning('VAT-ID-Live-Check-Usage-CleanUp failed.',
                                   array_merge([
                                                   'ip' => $ip,
                                               ],
                                               $this->getContextFromThrowable($e)));
            
            return false;
        }
    }
}