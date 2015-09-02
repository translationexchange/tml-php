<?php
/**
 * Copyright (c) 2015 Translation Exchange, Inc
 *
 *  _______                  _       _   _             ______          _
 * |__   __|                | |     | | (_)           |  ____|        | |
 *    | |_ __ __ _ _ __  ___| | __ _| |_ _  ___  _ __ | |__  __  _____| |__   __ _ _ __   __ _  ___
 *    | | '__/ _` | '_ \/ __| |/ _` | __| |/ _ \| '_ \|  __| \ \/ / __| '_ \ / _` | '_ \ / _` |/ _ \
 *    | | | | (_| | | | \__ \ | (_| | |_| | (_) | | | | |____ >  < (__| | | | (_| | | | | (_| |  __/
 *    |_|_|  \__,_|_| |_|___/_|\__,_|\__|_|\___/|_| |_|______/_/\_\___|_| |_|\__,_|_| |_|\__, |\___|
 *                                                                                        __/ |
 *                                                                                       |___/
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

namespace Tml\Utils;

require_once(__DIR__."/../../BaseTest.php");

class BrowserUtilsTest extends \BaseTest
{

    public function testParseLanguageList()
    {
        $result = BrowserUtils::parseLanguageList();
        $this->assertEquals(array(), $result);

        $languages = "en,es;q=0.8,zh;q=0.6,ru;";
        $_SERVER["HTTP_ACCEPT_LANGUAGE"] = $languages;
        $result = BrowserUtils::parseLanguageList();
        $this->assertEquals(array("1.0" => array("en", "ru"), "0.8" => array("es"), "0.6" => array("zh")), $result);

        $result = BrowserUtils::parseLanguageList($languages);
        $this->assertEquals(array("1.0" => array("en", "ru"), "0.8" => array("es"), "0.6" => array("zh")), $result);
    }

    public function testFindMatches() {
        $languages = "en,es;q=0.8,zh;q=0.6,ru;q=0.4,en-US;q=0.2,zh-CN;q=0.2,ja;q=0.2,de;q=0.2,fr;q=0.2,ar;q=0.2";
        $matches = BrowserUtils::findMatches(BrowserUtils::parseLanguageList($languages), array("1" => array("ru")));
//        var_dump($matches);

        $this->assertEquals(array("0.4" => array("ru")), $matches);
    }
}