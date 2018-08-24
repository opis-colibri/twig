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

namespace Opis\Colibri\Modules\Twig;

use Twig\{
    TwigFunction,
    TwigFilter,
    Environment as TwigEnvironment,
    Extension\ExtensionInterface as TwigExtension
};
use Opis\View\{
    IEngine, ViewRenderer
};
use Opis\Colibri\Serializable\ClassList;
use function Opis\Colibri\Functions\{
    app, info
};

class TwigEngine implements IEngine
{
    /** @var TwigEnvironment  */
    protected $twig;

    /**
     * TwigEngine constructor.
     * @param ViewRenderer $renderer
     */
    public function __construct(ViewRenderer $renderer)
    {
        $info = info();

        $this->twig = new TwigEnvironment(new TwigFileLoader($renderer, $info->rootDir()), [
            'cache' => $info->writableDir() . '/twig',
            'auto_reload' => true,
        ]);

        $this->initEnvironment($this->twig);
    }

    /**
     * @param TwigEnvironment $twig
     */
    protected function initEnvironment(TwigEnvironment $twig)
    {
        if(info()->installMode()){
            $functions = $filters = $extensions = [];
        } else {
            $collector = app()->getCollector();

            /** @var array $functions */
            $functions = $collector->collect(Collector\TwigFunctionCollector::NAME)->getList();

            /** @var array $filters */
            $filters = $collector->collect(Collector\TwigFilterCollector::NAME)->getList();

            /** @var TwigExtension[] $extensions */
            $extensions = [];
            /** @var ClassList $items */
            $items = $collector->collect(Collector\TwigExtensionCollector::NAME);
            foreach ($items as $name) {
                $extensions[] = $items->get($name);
            }
            unset($items);
        }

        $ns = 'Opis\Colibri\Functions\\';

        $functions += [
            'asset' => ['callback' => $ns . 'asset', 'options' => []],
            'csrf' => ['callback' => $ns . 'generateCSRFToken', 'options' => []],
            'url' => ['callback' => $ns . 'getURL', 'options' => []],
            't' => ['callback' => $ns . 't', 'options' => []],
            'r' => ['callback' => $ns . 'r', 'options' => []],
            'view' => ['callback' => $ns . 'view', 'options' => ['is_safe' => ['html']]],
            'render' => ['callback' => $ns . 'render', 'options' => ['is_safe' => ['html']]],
        ];

        $filters += [
            't' => ['callback' => $ns . 't', 'options' => []],
            'r' => ['callback' => $ns . 'r', 'options' => []],
            'url' => ['callback' => $ns . 'getURL', 'options' => []],
        ];

        foreach ($functions as $name => $item){
            if (empty($item['callback'])) {
                continue;
            }
            $twig->addFunction(new TwigFunction($name, $item['callback'], $item['options'] ?? []));
        }

        foreach ($filters as $name => $item){
            if (empty($item['callback'])) {
                continue;
            }
            $twig->addFilter(new TwigFilter($name, $item['callback'], $item['options'] ?? []));
        }

        foreach ($extensions as $ext) {
            $twig->addExtension($ext);
        }
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
        return (bool) preg_match('/^.*\.twig$/', $path);
    }


    /**
     * @param ViewRenderer $renderer
     * @return TwigEngine
     */
    public static function factory(ViewRenderer $renderer): self
    {
        return new static($renderer);
    }
}
