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

namespace Opis\Colibri\Module\Welcome;

use Opis\Colibri\App;
use Twig_LoaderInterface;

class TwigFileLoader implements Twig_LoaderInterface
{   
    public function getSource($name)
    {
        return file_get_contents($this->find($name));
    }

    public function getCacheKey($name)
    {
        return md5($this->find($name));
    }

    public function isFresh($name, $time)
    {
        return filemtime($this->find($name)) < $time;
    }
    
    protected function find($name)
    {
        if(file_exists($name))
        {
            return $name;
        }
        
        return App::systemView()->resolveViewName($name);
    }
}
