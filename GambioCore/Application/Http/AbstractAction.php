<?php
/* --------------------------------------------------------------
 AbstractAction.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Http;

use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class AbstractAction
 *
 * @package Gambio\Core\Application\Http
 * @codeCoverageIgnore
 */
abstract class AbstractAction
{
    /**
     * @var Url
     */
    protected $url;
    
    /**
     * @var TextManager
     */
    protected $textManager;
    
    
    /**
     * Module action initialization.
     *
     * @param Url         $url
     * @param TextManager $textManager
     */
    public function initAbstractAction(Url $url, TextManager $textManager): void
    {
        $this->url         = $url;
        $this->textManager = $textManager;
    }
    
    
    /**
     * Translates the section phrase.
     *
     * @param string   $phrase
     * @param string   $section
     * @param int|null $languageId
     *
     * @return string
     */
    protected function translate(string $phrase, string $section, int $languageId = null): string
    {
        return $this->textManager->getPhraseText($phrase, $section, $languageId);
    }
    
    
    /**
     * This method will be called in order to process the incoming request.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->handle($request, $response);
    }
    
    
    /**
     * Executes the business logic of the incoming request.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    abstract public function handle(Request $request, Response $response): Response;
}