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

namespace Opis\Colibri\Modules\Twig\Collector;

class TwigContainer
{

    protected array $data = [];

    /**
     * @param string $name
     * @param callable $callback
     * @param array $options
     * @return $this
     */
    public function register(string $name, callable $callback, array $options = []): self
    {
        $this->data[$name] = [
            'callback' => $callback,
            'options' => $options
        ];

        return $this;
    }

    public function getList(): array
    {
        return $this->data;
    }

    public function __serialize(): array
    {
        return $this->data;
    }

    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }
}