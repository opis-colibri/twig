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

namespace OpisColibri\Twig;

use Opis\View\ViewApp;
use Twig_Environment;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Opis\View\EngineInterface;
use function Opis\Colibri\Functions\{
    info
};

class TwigEngine implements EngineInterface
{
    /** @var Twig_Environment  */
    protected $twig;

    public function __construct()
    {

        $this->twig = new Twig_Environment(new TwigFileLoader(), [
            'cache' => info()->writableDir() . '/twig',
            'auto_reload' => true,
        ]);
       
        $ns = 'Opis\Colibri\Functions\\';
        

        $this->twig->addFilter(new Twig_SimpleFilter('t', $ns . 't'));
        $this->twig->addFunction(new Twig_SimpleFunction('t', $ns . 't'));

        $this->twig->addFunction(new Twig_SimpleFunction('asset', $ns . 'asset'));

        $this->twig->addFilter(new Twig_SimpleFilter('url', $ns . 'getURL'));
        $this->twig->addFunction(new Twig_SimpleFunction('url', $ns . 'getURL'));

        $this->twig->addFilter(new Twig_SimpleFilter('v', $ns . 'v'));
        $this->twig->addFunction(new Twig_SimpleFunction('v', $ns . 'v'));

        $this->twig->addFilter(new Twig_SimpleFilter('r', $ns . 'r'));
        $this->twig->addFunction(new Twig_SimpleFunction('r', $ns . 'r'));

        $this->twig->addFunction(new Twig_SimpleFunction('csrf', $ns . 'generateCSRFToken'));

        $safe = ['is_safe' => ['html']];

        $this->twig->addFunction(new Twig_SimpleFunction('view', $ns . 'view', $safe));
        $this->twig->addFunction(new Twig_SimpleFunction('render', $ns . 'render', $safe));
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
     * @param ViewApp $viewApp
     * @return TwigEngine
     */
    public static function factory(ViewApp $viewApp): self
    {
        return new static();
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
