<?php
/* ===========================================================================
 * Copyright 2019-2020 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Colibri\Modules\Twig;

use Throwable;
use Opis\View\Renderer;
use Twig\Extension\SandboxExtension;
use Twig\Sandbox\SecurityPolicyInterface;

class SecureTwigEngine extends TwigEngine
{
    /**
     * SecureTwigEngine constructor.
     * @param Renderer $renderer
     * @param SecurityPolicyInterface $policy
     */
    public function __construct(Renderer $renderer, SecurityPolicyInterface $policy)
    {
        parent::__construct($renderer);
        $this->regex = '/^.*\.twig\-secure$/';
        $this->twig->addExtension(new SandboxExtension($policy, true));
    }

    /**
     * @inheritDoc
     */
    public function build(string $path, array $vars = []): string
    {
        try {
            return $this->twig->render($path, $vars);
        } catch (\Throwable $exception) {
            return $this->errorMessage($exception);
        }
    }

    /**
     * @param Throwable $exception
     * @return string
     */
    protected function errorMessage(Throwable $exception): string
    {
        return $exception->getMessage();
    }
}