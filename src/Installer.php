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

use Opis\Colibri\Installer as AbstractInstaller;
use function Opis\Colibri\Functions\{
    app, info
};

class Installer extends AbstractInstaller
{


    public function enable()
    {
        $collector = app()->getCollector();
        $collector->register(Collector\TwigFunctionCollector::NAME, Collector\TwigFunctionCollector::class,
            'Collect twig functions');
        $collector->register(Collector\TwigFilterCollector::NAME, Collector\TwigFilterCollector::class,
            'Collect twig filters');
    }

    public function disable()
    {
        $collector = app()->getCollector();
        $collector->unregister(Collector\TwigFunctionCollector::NAME);
        $collector->unregister(Collector\TwigFilterCollector::NAME);
    }

    public function uninstall()
    {
        $rmdir = function($path) use(&$rmdir){
            if (is_dir($path) === true) {

                $files = array_diff(scandir($path), array('.', '..'));
                
                foreach ($files as $file) {
                    $rmdir(realpath($path) . '/' . $file);
                }
                
                return rmdir($path);
            } elseif(is_file($path) === true) {
                return unlink($path);
            }
            return false;
        };
        
        $rmdir(info()->writableDir() . '/twig');
    }
}
