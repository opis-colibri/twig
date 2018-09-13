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

use Opis\View\ViewRenderer;
use Twig\Source as TwigSource;
use Twig\Error\LoaderError as TwigLoaderError;
use Twig\Loader\LoaderInterface as TwigLoaderInterface;

class TwigFileLoader implements TwigLoaderInterface
{
    /** @var ViewRenderer */
    protected $renderer;

    /** @var string|null */
    protected $root = null;

    /** @var int */
    protected $rootLen = 0;

    /**
     * TwigFileLoader constructor.
     * @param ViewRenderer $renderer
     * @param string|null $rootPath Used for cacheKey
     */
    public function __construct(ViewRenderer $renderer, string $rootPath = null)
    {
        $this->renderer = $renderer;
        if ($rootPath !== null) {
            $this->root = trim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $this->rootLen = strlen($this->root);
        }
    }

    /**
     * @inheritDoc
     */
    public function getSourceContext($name)
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
    public function getCacheKey($name)
    {
        $path = $this->find($name);
        if ($path === null) {
            throw new TwigLoaderError("View {$name} was not found");
        }

        // If path is a local file, strip root path
        // In this way you can move the app to another dir, without a cache rebuild
        if ($this->rootLen > 0 && strpos($path, $this->root) === 0) {
            $path = substr($path, $this->rootLen);
        }

        return md5($path);
    }

    /**
     * @inheritDoc
     */
    public function isFresh($name, $time)
    {
        $path = $this->find($name);

        if ($path === null) {
            throw new TwigLoaderError("View {$name} was not found");
        }

        return filemtime($this->find($name)) < $time;
    }

    /**
     * @inheritDoc
     */
    public function exists($name)
    {
        return $this->find($name) !== null;
    }

    /**
     * @param $name
     * @return string|null
     */
    protected function find(string $name)
    {
        if (file_exists($name)) {
            return $name;
        }

        return $this->renderer->resolveViewName($name);
    }
}
