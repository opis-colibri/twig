<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2015 Marius Sarca
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

use Opis\Colibri\ModuleCollector;

class Twig extends ModuleCollector
{

    /**
     * Collect view engines
     */
    public function viewEngines($engine, $app)
    {
        $engine->register(function($app) {
            return new TwigEngine($app);
        })
        ->handle(function($path) {
            return preg_match('/^.*\.twig$/', $path);
        });
    }
}
