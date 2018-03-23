<?php
/* ===========================================================================
 * Copyright 2013-2018 The Opis Project
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

namespace OpisColibri\Twig\Collector;

use Opis\Colibri\ItemCollector;

/**
 * Class AbstractCollector
 * @package OpisColibri\Twig\Collector
 *
 * @property TwigContainer $data
 */
class AbstractCollector extends ItemCollector
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct(new TwigContainer());
    }

    /**
     * @param string $name
     * @param callable $callback
     * @param array $options
     * @return $this
     */
    public function register(string $name, callable $callback, array $options = [])
    {
        $this->data->register($name, $callback, $options);
        return $this;
    }
}
