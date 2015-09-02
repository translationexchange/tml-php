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

class ArrayUtilsTest extends \BaseTest {

    public function testFlatten() {
        $result = ArrayUtils::flatten(array("hello", array("new", "world")));
        $this->assertEquals(array("hello", "new", "world"), $result);
    }

    public function testNormalizeTmlParams() {
        $result = ArrayUtils::normalizeTmlParameters("Hello World");
        $this->assertEquals(array("label" => "Hello World", "description" => "", "tokens" => array(), "options" => array()), $result);

        $result = ArrayUtils::normalizeTmlParameters("Hello World", "Test");
        $this->assertEquals(array("label" => "Hello World", "description" => "Test", "tokens" => array(), "options" => array()), $result);

        $result = ArrayUtils::normalizeTmlParameters("Hello {user}", "Test", array("user" => "Michael"));
        $this->assertEquals(array("label" => "Hello {user}", "description" => "Test", "tokens" => array("user" => "Michael"), "options" => array()), $result);

        $result = ArrayUtils::normalizeTmlParameters("Hello {user}", "Test", array("user" => "Michael"), array("test" => true));
        $this->assertEquals(array("label" => "Hello {user}", "description" => "Test", "tokens" => array("user" => "Michael"), "options" => array("test" => true)), $result);

        $result = ArrayUtils::normalizeTmlParameters("Hello {user}", array("user" => "Michael"));
        $this->assertEquals(array("label" => "Hello {user}", "description" => "", "tokens" => array("user" => "Michael"), "options" => array()), $result);

        $result = ArrayUtils::normalizeTmlParameters("Hello {user}", array("user" => "Michael"), array("test" => true));
        $this->assertEquals(array("label" => "Hello {user}", "description" => "", "tokens" => array("user" => "Michael"), "options" => array("test" => true)), $result);
    }

    public function testCreateAtribute() {
        $test = array();
        ArrayUtils::createAttribute($test, array("user", "name"), "Michael");
        $this->assertEquals(array("user" => array("name" => "Michael")), $test);
    }

    public function testGetAtribute() {
        $test = array("user" => array("name" => array("first" => "Michael")));
        $result = ArrayUtils::getAttribute($test, array("user", "name", "first"));
        $this->assertEquals("Michael", $result);

        $result = ArrayUtils::getAttribute($test, array("user", "name", "last"));
        $this->assertNull($result);

        $result = ArrayUtils::getAttribute($test, array("user", "age"));
        $this->assertNull($result);
    }

    public function testToHtmlAttributes() {
        $result = ArrayUtils::toHTMLAttributes(array("style" => "color:red", "class" => "test"));
        $this->assertEquals('style="color:red" class="test"', $result);
    }

    public function testSplit() {
        $result = ArrayUtils::split("This is a test", " ");
        $this->assertEquals(array("This", "is", "a", "test"), $result);
    }

    public function testTrim() {
      $data = array(
        array(  "source" =>  "/posts/privacy_policy",
                "keys"  =>
                        array(
                            array(  "label"=>"Hello {user}",
                                    "description"=>null,
                                    "locale"=>"en",
                                    "level"=>0),
                            array(
                                    "label"=>"Documentation",
                                    "description"=>null,
                                    "locale"=>"en",
                                    "level"=>0),
                            array(  "label"=>"Research your family history easily and instantly:",
                                    "description"=>null,
                                    "locale"=>"en",
                                    "level"=>0),
                            array(  "label"=> "MyHeritage was founded by a team of people with a passion for genealogy and a strong grasp of Internet technology.",
                                    "description"=>null,
                                    "locale"=>"en",
                                    "level"=>0)
                            )
                )
      );

      $data = ArrayUtils::trim($data);
      $this->assertEquals(
          array(
              array(  "source" =>  "/posts/privacy_policy",
                  "keys"  =>
                  array(
                      array(  "label"=>"Hello {user}",
                          "locale"=>"en",
                          ),
                      array(
                          "label"=>"Documentation",
                          "locale"=>"en",
                          ),
                      array(  "label"=>"Research your family history easily and instantly:",
                          "locale"=>"en",
                          ),
                      array(  "label"=> "MyHeritage was founded by a team of people with a passion for genealogy and a strong grasp of Internet technology.",
                          "locale"=>"en",
                          )
                  )
              )
          ),
          $data
      );
    }
}

