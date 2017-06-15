<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

namespace OpisColibri\Twig;

use Opis\Colibri\Collectors\ViewEngineCollector;
use Opis\Colibri\ModuleCollector;

class Collector extends ModuleCollector
{
    /**
     * Collect view engines
     * 
     * @param ViewEngineCollector $engine
     */
    public function viewEngines(ViewEngineCollector $engine)
    {
        $engine->register(TwigEngine::class . '::factory')
               ->handle(TwigEngine::class . '::pathHandler');
    }

    /**
     * @param Collector\TwigFunctionCollector $functions
     */
    public function twigFunctions(Collector\TwigFunctionCollector $functions)
    {
        $ns = 'Opis\Colibri\Functions\\';
        $functions->register('asset', $ns . 'asset');
        $functions->register('csrf', $ns . 'generateCSRFToken');

        $functions->register('t', $ns . 't');
        $functions->register('r', $ns . 'r');
        $functions->register('v', $ns . 'v');

        $functions->register('view', [
            'callback' => $ns . 'view',
            'options' => ['is_safe' => ['html']]
        ]);
        $functions->register('render', [
            'callback' => $ns . 'render',
            'options' => ['is_safe' => ['html']]
        ]);
    }

    /**
     * @param Collector\TwigFilterCollector $filters
     */
    public function twigFilters(Collector\TwigFilterCollector $filters)
    {
        $ns = 'Opis\Colibri\Functions\\';
        $filters->register('t', $ns . 't');
        $filters->register('r', $ns . 'r');
        $filters->register('v', $ns . 'v');
    }
}
