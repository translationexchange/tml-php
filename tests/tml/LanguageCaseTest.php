<?php
/**
 * Copyright (c) 2013 Michael Berkovich, tmlhub.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace tml;

require_once(__DIR__."/../BaseTest.php");

class LanguageCaseTest extends \BaseTest {

    protected $app, $russian, $english;

    protected function setUp() {
        $this->app = new Application(self::loadJSON('application.json'));
        $this->english = $this->app->addLanguage(new Language(self::loadJSON('languages/en.json')));
        $this->russian = $this->app->addLanguage(new Language(self::loadJSON('languages/ru.json')));
    }

    public function testApplication() {
        $case = $this->english->languageCase("plural");

        $this->assertEquals(
            "sheep",
            $case->apply('sheep')
        );

        $this->assertEquals(
            "motors",
            $case->apply('motor')
        );

        $this->assertEquals(
            "people",
            $case->apply('person')
        );

        $this->assertEquals(
            "information",
            $case->apply('information')
        );

        $case = $this->english->languageCase("times");

        $this->assertEquals(
            "once",
            $case->apply(1)
        );

        $this->assertEquals(
            "twice",
            $case->apply(2)
        );

        $this->assertEquals(
            "3 times",
            $case->apply(3)
        );

        $case = $this->english->languageCase("pos");
        $this->assertEquals(
            "Michael's",
            $case->apply("Michael")
        );

        $case = $this->english->languageCase("pos");
        $this->assertEquals(
            "cars'",
            $case->apply("cars")
        );

    }


}

