<?php
/* --------------------------------------------------------------
   LoggingService.php 2020-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Logging;

use Psr\Log\LoggerInterface;

/**
 * Interface LoggingService
 *
 * @package Gambio\Core\Logging
 */
interface LoggerBuilder
{
    /**
     * Changes the namespace for new created logger.
     *
     * @param string $namespace
     *
     * @return self
     */
    public function changeNamespace(string $namespace = 'general'): self;
    
    
    /**
     * Adds HTTP request data to logs of new created logger.
     *
     * @return self
     */
    public function addRequestData(): self;
    
    
    /**
     * Omits HTTP request data for logs of new created logger.
     *
     * @return self
     */
    public function omitRequestData(): self;
    
    
    /**
     * Builds a logger.
     *
     * @return LoggerInterface
     */
    public function build(): LoggerInterface;
}