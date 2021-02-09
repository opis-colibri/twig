<?php
/* ===========================================================================
 * Copyright 2018-2021 Zindex Software
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

use Twig\Source as TwigSource;
use Opis\Colibri\Render\Renderer;
use Twig\Error\LoaderError as TwigLoaderError;
use Twig\Loader\LoaderInterface as TwigLoaderInterface;

class TwigFileLoader implements TwigLoaderInterface
{
    protected Renderer $renderer;
    protected ?string $root = null;
    protected int $rootLen = 0;

    public function __construct(Renderer $renderer, ?string $rootPath = null)
    {
        $this->renderer = $renderer;
        if ($rootPath !== null) {
            $this->root = trim($rootPath, '/') . '/';
            $this->rootLen = strlen($this->root);
        }
    }

    /**
     * @inheritDoc
     */
    public function getSourceContext(string $name): TwigSource
    {
        $path = $this->find($name);

        if ($path === null) {
            throw new TwigLoaderError("View {$name} was not found");
        }

        return new TwigSource(file_get_contents($path), $name, $path);
    }

    /**
     * @inheritDoc
     */
    public function getCacheKey(string $name): string
    {
        $path = $this->find($name);
        if ($path === null) {
            throw new TwigLoaderError("View {$name} was not found");
        }

        // If path is a local file, strip root path
        // In this way you can move the app to another dir, without a cache rebuild
        if ($this->rootLen > 0 && str_starts_with($path, $this->root)) {
            $path = substr($path, $this->rootLen);
        }

        return md5($path);
    }

    /**
     * @inheritDoc
     */
    public function isFresh(string $name, int $time): bool
    {
        $path = $this->find($name);

        if ($path === null) {
            throw new TwigLoaderError("View {$name} was not found");
        }

        return filemtime($path) < $time;
    }

    /**
     * @inheritDoc
     */
    public function exists(string $name): bool
    {
        return $this->find($name) !== null;
    }

    /**
     * @param $name
     * @return string|null
     */
    protected function find(string $name): ?string
    {
        if (file_exists($name)) {
            return $name;
        }

        return $this->renderer->resolveViewName($name);
    }
}
