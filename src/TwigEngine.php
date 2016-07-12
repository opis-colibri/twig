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

namespace OpisColibri\Twig;

use Twig_Environment;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Opis\Colibri\Application;
use Opis\View\EngineInterface;

class TwigEngine implements EngineInterface
{
    /** @var Twig_Environment  */
    protected $twig;

    /**
     * TwigEngine constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $helper = $app->getHelper();

        $this->twig = new Twig_Environment(new TwigFileLoader($app), [
            'cache' => $helper->writableDir() . '/twig',
            'auto_reload' => true,
        ]);

        $this->twig->addFilter(new Twig_SimpleFilter('t', [$helper, 't']));
        $this->twig->addFunction(new Twig_SimpleFunction('t', [$helper, 't']));

        $this->twig->addFunction(new Twig_SimpleFunction('asset', [$helper, 'getAsset']));

        $this->twig->addFilter(new Twig_SimpleFilter('url', [$helper, 'getURL']));
        $this->twig->addFunction(new Twig_SimpleFunction('url', [$helper, 'getURL']));

        $this->twig->addFilter(new Twig_SimpleFilter('v', [$helper, 'v']));
        $this->twig->addFunction(new Twig_SimpleFunction('v', [$helper, 'v']));

        $this->twig->addFilter(new Twig_SimpleFilter('r', [$helper, 'r']));
        $this->twig->addFunction(new Twig_SimpleFunction('r', [$helper, 'r']));

        $this->twig->addFunction(new Twig_SimpleFunction('csrf', [$helper, 'generateCSRFToken']));

        $safe = ['is_safe' => ['html']];

        $this->twig->addFunction(new Twig_SimpleFunction('view', [$helper, 'view'], $safe));
        $this->twig->addFunction(new Twig_SimpleFunction('render', [$helper, 'render'], $safe));
    }

    /**
     * @param string $path
     * @param array $data
     * @return string
     */
    public function build(string $path, array $data = array()): string
    {
        return $this->twig->render($path, $data);
    }

    /**
     * @param Application $app
     * @return TwigEngine
     */
    public static function factory(Application $app): self
    {
        return new static($app);
    }

    /**
     * @param string $path
     * @return bool
     */
    public static function pathHandler(string $path): bool
    {
        return preg_match('/^.*\.twig$/', $path);
    }
}
