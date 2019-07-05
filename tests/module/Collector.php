<?php
/* ============================================================================
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

namespace Test\Twig;

use Opis\Colibri\Collector as BaseCollector;
use Opis\Colibri\ItemCollectors\{TranslationCollector, ViewCollector};
use Opis\Colibri\Modules\Twig\Collector\{TwigFilterCollector, TwigFunctionCollector};

class Collector extends BaseCollector
{
    /**
     * @param ViewCollector $view
     */
    public function views(ViewCollector $view)
    {
        $view->handle('{name}.twig', function ($name) {
            return __DIR__ . "/../views/{$name}.twig";
        });
    }

    /**
     * @param TranslationCollector $translation
     */
    public function translations(TranslationCollector $translation)
    {
        $translation->addTranslations('example', [
            'key1' => 'T-KEY1',
        ]);
    }

    /**
     * @param TwigFilterCollector $filter
     */
    public function twigFilters(TwigFilterCollector $filter)
    {
        $filter->register('myFilter', function ($value) {
           return 'filtered:' . $value;
        });
    }

    /**
     * @param TwigFunctionCollector $func
     */
    public function twigFunctions(TwigFunctionCollector $func)
    {
        $func->register('sum_numbers', function ($a, $b) {
            return $a + $b;
        });
    }
}