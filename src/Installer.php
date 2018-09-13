<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

use Opis\Colibri\Installer as AbstractInstaller;
use function Opis\Colibri\Functions\{
    app, info
};
use Opis\Colibri\Modules\Twig\Collector\{
    TwigFunctionCollector, TwigExtensionCollector, TwigFilterCollector
};

class Installer extends AbstractInstaller
{
    public function enable()
    {
        $collector = app()->getCollector();
        $collector->register(TwigFunctionCollector::NAME, TwigFunctionCollector::class,
            'Collect twig functions');
        $collector->register(TwigFilterCollector::NAME, TwigFilterCollector::class,
            'Collect twig filters');
        $collector->register(TwigExtensionCollector::NAME, TwigExtensionCollector::class,
            'Collect twig extensions');
    }

    public function disable()
    {
        $collector = app()->getCollector();
        $collector->unregister(TwigFunctionCollector::NAME);
        $collector->unregister(TwigFilterCollector::NAME);
        $collector->unregister(TwigExtensionCollector::NAME);
    }

    public function uninstall()
    {
        $rmdir = function ($path) use (&$rmdir) {
            if (is_dir($path)) {

                $files = array_diff(scandir($path), ['.', '..']);

                foreach ($files as $file) {
                    $rmdir(realpath($path) . '/' . $file);
                }

                return rmdir($path);
            } elseif (is_file($path)) {
                return unlink($path);
            }
            return false;
        };

        $rmdir(info()->writableDir() . '/twig');
    }
}
