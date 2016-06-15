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

class StringUtilsTest extends \BaseTest {

    public function testStartsWith() {
        $this->assertTrue(StringUtils::startsWith(array("test", "test1"), "test"));
        $this->assertTrue(StringUtils::startsWith(array("test", "test1"), "test 124"));
        $this->assertTrue(StringUtils::startsWith(array("test", "test1"), "test1"));
        $this->assertFalse(StringUtils::startsWith(array("test", "test1"), "thetest1"));
        $this->assertTrue(StringUtils::startsWith("the", "thetest"));
    }

    public function testEndsWith() {
        $this->assertTrue(StringUtils::endsWith(array("test", "test1"), "test"));
        $this->assertFalse(StringUtils::endsWith(array("test", "test1"), "test 124"));
        $this->assertTrue(StringUtils::endsWith(array("test", "test1"), "test1"));
        $this->assertTrue(StringUtils::endsWith(array("test", "test1"), "thetest1"));
        $this->assertTrue(StringUtils::endsWith("st", "thetest"));
    }

    public function testSplitToSentences() {
        $text = "Hello World";
        $matches = StringUtils::splitSentences($text);
        $this->assertEquals("Hello World", $matches[0]);

        $text = "This is the first sentence. Followed by the second one.";
        $matches = StringUtils::splitSentences($text);
        $this->assertEquals(2, count($matches));

        $text = "Genealogical societies are essential to family history researchers. They provide resources, programs, conferences, and other important assistance. MyHeritage is spotlighting these societies in a new series over the year.";
        $matches = StringUtils::splitSentences($text);
        $this->assertEquals(3, count($matches));

        $text = "<br />
Genealogical societies are essential to family history researchers. </p>
<p>They provide resources, programs, conferences, and other important assistance. MyHeritage is spotlighting these societies in a new series over the year.<br />";
        $matches = StringUtils::splitSentences($text);
        $this->assertEquals(3, count($matches));
    }

    public function testPrettyPrint() {
        $data = array(
            array(  "source" =>  "/posts/privacy_policy",
                "keys"  =>
                    array(
                        array(  "label"=>"Hello {user}",
                            "locale"=>"en",
                        ),
                        array(
                            "label"=>"Documentation",
                            "locale"=>"en",
                        )
                    )
            )
        );

        $result = StringUtils::prettyPrint(json_encode($data));
        $this->assertEquals('[
	{
		"source": "\/posts\/privacy_policy",
		"keys": [
			{
				"label": "Hello {user}",
				"locale": "en"
			},
			{
				"label": "Documentation",
				"locale": "en"
			}
		]
	}
]', $result);

    }

    public function testMatchStrings() {
        $source_mapping = array(
            "/\\/viewitinerary\\/[\\d]*/" => "/viewitinerary/show",
            "/\\/guide\\/scope\\/[\\d]*/" => "/guide/scope/show",
        );

        $result = StringUtils::matchSource($source_mapping, "/viewitinerary/158029");
        $this->assertEquals("/viewitinerary/show", $result);

        $result = StringUtils::matchSource($source_mapping, "/guide/scope/223");
        $this->assertEquals("/guide/scope/show", $result);

        $result = StringUtils::matchSource($source_mapping, "/guide/scopes");
        $this->assertEquals("/guide/scopes", $result);

        $result = StringUtils::matchSource($source_mapping, "other");
        $this->assertEquals("other", $result);
    }
}