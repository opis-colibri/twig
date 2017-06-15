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

namespace OpisColibri\Twig\Collector;

use Opis\Colibri\Collector;

/**
 * Class AbstractCollector
 * @package OpisColibri\Twig\Collector
 *
 * @method TwigContainer data()
 * @property TwigContainer $dataObject
 */
class AbstractCollector extends Collector
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
     * @param callable|null $callback
     * @param array $options
     * @return $this
     */
    public function register(string $name,callable $callback = null, array $options = [])
    {
        $this->dataObject->register($name, $callback, $options);
        return $this;
    }

}
