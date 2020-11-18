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

namespace Opis\Colibri\Modules\Twig\Test;

use Opis\Colibri\Testing\ApplicationTestCase;
use Opis\Colibri\Testing\Builders\ApplicationBuilder;

class BaseClass extends ApplicationTestCase
{

    protected static function vendorDir(): string
    {
        return __DIR__ . '/../vendor';
    }

    protected static function setupApp(ApplicationBuilder $builder): void
    {
        $builder->addUninstalledModuleFromPath(__DIR__ . '/../');

        $builder->createInstalledTestModule('test/twig', 'Test\\Twig', __DIR__ . '/module', [
            'collector' => 'Test\\Twig\\Collector',
        ], ['opis-colibri/twig']);

        $builder->addDependencies('test/twig');
    }
}