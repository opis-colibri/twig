<?php
/* ============================================================================
 * Copyright 2018-2019 Zindex Software
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

use function Opis\Colibri\Functions\view as v;

class RenderTest extends BaseClass
{
    public function testVariables()
    {
        $this->assertEquals("test-OK", v('variables.twig', [
            'a' => 'test',
            'b' => 'ok'
        ]));
    }

    public function testLoops()
    {
        $this->assertEquals("- 1\n- 2\n- 3\n", v('loop.twig', [
            'items' => [1, 2, 3]
        ]));
    }

    public function testInclude()
    {
        $this->assertEquals("Including included.twig\nThis is included", v('include.twig', [
            'file' => 'included.twig'
        ]));
    }

    public function testTranslation()
    {
        $this->assertEquals('T-KEY1', v('t.twig', ['key' => 'example:key1']));
    }

    public function testEscape()
    {
        $this->assertEquals("&lt;b&gt;escaped&lt;/b&gt;\n", v('escape.twig', [
            'escape' => true,
            'content' => '<b>escaped</b>'
        ]));

        $this->assertEquals("<b>escaped</b>\n", v('escape.twig', [
            'escape' => false,
            'content' => '<b>escaped</b>'
        ]));
    }

    public function testFilters()
    {
        $this->assertEquals('filtered:message', v('filter.twig', [
            'content' => 'message'
        ]));
    }

    public function testFunctions()
    {
        $this->assertEquals('Sum is: 5', v('func.twig', [
            'a' => 10,
            'b' => -5,
        ]));
    }
}