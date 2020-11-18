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

use Opis\View\{Engine, Renderer};
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\ExtensionInterface as TwigExtension;
use function Opis\Colibri\{collect, info};

class TwigEngine implements Engine
{
    protected TwigEnvironment $twig;
    protected string $regex;

    /**
     * TwigEngine constructor.
     * @param Renderer $renderer
     */
    public function __construct(Renderer $renderer)
    {
        $twig = $this->createEnvironment($renderer);

        $this->addFunctions($twig);
        $this->addFilters($twig);
        $this->addExtensions($twig);

        $this->twig = $twig;
        $this->regex = '/^.*\.twig$/';
    }

    /**
     * @param string $path
     * @param array $data
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function build(string $path, array $data = []): string
    {
        return $this->twig->render($path, $data);
    }

    /**
     * @inheritDoc
     */
    public function canHandle(string $path): bool
    {
        return (bool) preg_match($this->regex, $path);
    }

    /**
     * @param Renderer $renderer
     * @return TwigEnvironment
     */
    protected function createEnvironment(Renderer $renderer): TwigEnvironment
    {
        $info = info();
        return new TwigEnvironment(new TwigFileLoader($renderer, $info->rootDir()), [
            'cache' => $info->writableDir() . '/twig',
            'auto_reload' => true,
        ]);
    }

    /**
     * @param TwigEnvironment $twig
     */
    protected function addFunctions(TwigEnvironment $twig): void
    {
        $items = collect(Collector\TwigFunctionCollector::class)->getList();

        $ns = '\Opis\Colibri\\';
        $items += [
            'asset' => $ns . 'asset',
            'csrf' => $ns . 'generateCSRFToken',
            'url' => $ns . 'getURI',
            't' => $ns . 't',
            'tns' => $ns . 'tns',
            'view' => ['callback' => $ns . 'view', 'options' => ['is_safe' => ['html']]],
            'render' => ['callback' => $ns . 'render', 'options' => ['is_safe' => ['html']]],
        ];

        foreach ($items as $name => $item) {
            if (is_callable($item)) {
                $item = ['callback' => $item];
            } elseif (empty($item['callback'])) {
                continue;
            }
            $twig->addFunction(new TwigFunction($name, $item['callback'], $item['options'] ?? []));
        }
    }

    /**
     * @param TwigEnvironment $twig
     */
    protected function addFilters(TwigEnvironment $twig): void
    {
        $items = collect(Collector\TwigFilterCollector::class)->getList();

        $ns = '\Opis\Colibri\\';

        /** @var string[]|array[] $items */
        $items += [
            't' => $ns . 't',
            'tns' => $ns . 'tns',
            'url' => $ns . 'getURI',
        ];

        foreach ($items as $name => $item) {
            if (is_callable($item)) {
                $item = ['callback' => $item];
            } elseif (!isset($item['callback'])) {
                continue;
            }
            $twig->addFilter(new TwigFilter($name, $item['callback'], $item['options'] ?? []));
        }
    }

    /**
     * @param TwigEnvironment $twig
     */
    protected function addExtensions(TwigEnvironment $twig): void
    {
        $items = collect(Collector\TwigExtensionCollector::class);
        foreach ($items as $item) {
            /** @var TwigExtension $item */
            if ($item = $items->get($item)) {
                $twig->addExtension($item);
            }
        }
    }

    /**
     * @param Renderer $renderer
     * @return TwigEngine
     */
    public static function factory(Renderer $renderer): self
    {
        return new static($renderer);
    }
}
