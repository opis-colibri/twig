<?php
/* ===========================================================================
 * Copyright 2019 Zindex Software
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

use Exception;
use Opis\View\{
    IEngine, ViewRenderer
};
use Twig\{
    Environment as TwigEnvironment,
    Extension\SandboxExtension,
    Sandbox\SecurityPolicyInterface,
    TwigFilter,
    TwigFunction
};
use function Opis\Colibri\Functions\{app, info};

class SecureTwigEngine implements IEngine
{
    /** @var TwigEnvironment */
    protected $twig;

    /** @var string[] */
    protected $match;

    /** @var string|null */
    private $pattern;

    /**
     * SecureTwigEngine constructor.
     * @param ViewRenderer $renderer
     * @param SecurityPolicyInterface $policy
     * @param array $match
     */
    public function __construct(ViewRenderer $renderer, SecurityPolicyInterface $policy, array $match = ['twig-secure'])
    {
        $this->match = $match;

        $info = info();

        $this->twig = new TwigEnvironment(new TwigFileLoader($renderer, $info->rootDir()), [
            'cache' => $info->writableDir() . '/twig',
            'auto_reload' => true,
        ]);

        if ($info->installMode()) {
            $filters = $functions = [];
        } else {
            $collector = app()->getCollector();

            /** @var array $functions */
            $functions = $collector->collect(Collector\TwigFunctionCollector::NAME)->getList();

            /** @var array $filters */
            $filters = $collector->collect(Collector\TwigFilterCollector::NAME)->getList();
        }

        $this->initTwig($this->twig, $functions, $filters);

        $this->twig->addExtension(new SandboxExtension($policy, true));
    }

    /**
     * @inheritDoc
     */
    public function build(string $path, array $vars = []): string
    {
        try {
            return  $this->twig->render($path, $vars);
        } catch (\Exception $exception) {
            return $this->errorMessage($exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function canHandle(string $path): bool
    {
        if ($this->pattern === null) {
            $extensions = array_map(function(string $value){
                return preg_quote($value, '/');
            }, $this->match);
            $this->pattern = '/^.*\.(' . implode('|', $extensions) . ')$';
        }

        return preg_match($this->pattern, $path);
    }

    /**
     * @param TwigEnvironment $twig
     * @param array $functions
     * @param array $filters
     */
    protected function initTwig(TwigEnvironment $twig, array $functions, array $filters)
    {
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
    }

    /**
     * @param Exception $exception
     * @return string
     */
    protected function errorMessage(Exception $exception): string
    {
        return $exception->getMessage();
    }
}