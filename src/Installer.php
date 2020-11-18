<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
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

use Opis\Utils\Dir;
use Opis\Colibri\Installer as AbstractInstaller;
use Opis\Colibri\Modules\Twig\Collector\{
    TwigFunctionCollector, TwigExtensionCollector, TwigFilterCollector
};
use function Opis\Colibri\{app, info};

class Installer extends AbstractInstaller
{
    public function enable()
    {
        app()->getCollector()
            ->register(TwigFunctionCollector::class, 'Collect twig functions')
            ->register(TwigFilterCollector::class, 'Collect twig filters')
            ->register(TwigExtensionCollector::class, 'Collect twig extensions');
    }

    public function disable()
    {
        app()->getCollector()
            ->unregister(TwigFunctionCollector::class)
            ->unregister(TwigFilterCollector::class)
            ->unregister(TwigExtensionCollector::class);
    }

    public function uninstall()
    {
        Dir::remove(info()->writableDir() . '/twig');
    }
}
