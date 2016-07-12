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
    protected $twig;

    public function __construct(Application $app)
    {
        $this->twig = new Twig_Environment(new TwigFileLoader($app), array(
            'cache' => $app->info()->storagesPath() . '/twig',
            'auto_reload' => true,
        ));

        $this->twig->addFilter(new Twig_SimpleFilter('t', $app->getTranslator()));
        $this->twig->addFunction(new Twig_SimpleFunction('t', $app->getTranslator()));

        $this->twig->addFunction(new Twig_SimpleFunction('asset', array($app, 'asset')));

        $this->twig->addFilter(new Twig_SimpleFilter('url', array($app, 'getURL')));
        $this->twig->addFunction(new Twig_SimpleFunction('url', array($app, 'getURL')));

        $this->twig->addFilter(new Twig_SimpleFilter('path', array($app, 'getPath')));
        $this->twig->addFunction(new Twig_SimpleFunction('path', array($app, 'getPath')));

        $this->twig->addFilter(new Twig_SimpleFilter('var', array($app, 'variable')));
        $this->twig->addFunction(new Twig_SimpleFunction('var', array($app, 'variable')));

        $this->twig->addFunction(new Twig_SimpleFunction('csrf', array($app, 'csrfToken')));

        $safe = array('is_safe' => array('html'));

        $this->twig->addFunction(new Twig_SimpleFunction('view', array($app, 'view'), $safe));
        $this->twig->addFunction(new Twig_SimpleFunction('render', array($app->getViewRouter(), 'render'), $safe));
    }

    public function build($path, array $data = array())
    {
        return $this->twig->render($path, $data);
    }
}
